import { createWorker } from 'tesseract.js';
import * as pdfjs from 'pdfjs-dist';
import pdfWorkerUrl from 'pdfjs-dist/build/pdf.worker.min.mjs?url';

pdfjs.GlobalWorkerOptions.workerSrc = pdfWorkerUrl;

const units = 'pcs?|pieces?|nos?|kg|kgs|kilograms?|g|grams?|lit(?:re|er)s?|ml|box(?:es)?|packets?|pkt|dozens?|dz|met(?:re|er)s?|feet|ft';
const ignoredLine = /\b(?:subtotal|grand\s*total|taxable|discount|vat|tax|round(?:ing)?|amount\s+in\s+words|balance|net\s+amount)\b/i;

function number(value) {
    const parsed = Number(String(value || '').replace(/,/g, ''));
    return Number.isFinite(parsed) ? parsed : null;
}

function isoDate(value) {
    const match = String(value || '').match(/(\d{1,4})[\/.\-](\d{1,2})[\/.\-](\d{1,4})/);
    if (!match) return null;

    let year;
    let month;
    let day;
    if (match[1].length === 4) {
        year = Number(match[1]);
        month = Number(match[2]);
        day = Number(match[3]);
    } else {
        day = Number(match[1]);
        month = Number(match[2]);
        year = Number(match[3]);
        if (year < 100) year += 2000;
    }

    if (year < 2000 || year > 2100 || month < 1 || month > 12 || day < 1 || day > 31) return null;
    return `${year}-${String(month).padStart(2, '0')}-${String(day).padStart(2, '0')}`;
}

function cleanName(value) {
    return String(value || '')
        .replace(/^\s*(?:s\.?n\.?|sr\.?|#)?\s*\d+[.)\-]?\s+/i, '')
        .replace(/\s{2,}/g, ' ')
        .trim();
}

function normalizeUnit(value) {
    const unit = String(value || '').toLowerCase();
    if (/^(?:pc|pcs|piece|pieces|no|nos)$/.test(unit)) return 'pcs';
    if (/^(?:kg|kgs|kilogram|kilograms)$/.test(unit)) return 'kg';
    if (/^(?:g|gram|grams)$/.test(unit)) return 'g';
    if (/^(?:litre|liter|litres|liters)$/.test(unit)) return 'litre';
    if (unit === 'ml') return 'ml';
    if (/^box/.test(unit)) return 'box';
    if (/^(?:packet|packets|pkt)$/.test(unit)) return 'packet';
    if (/^(?:dozen|dozens|dz)$/.test(unit)) return 'dozen';
    if (/^(?:metre|meter|metres|meters)$/.test(unit)) return 'metre';
    if (/^(?:feet|ft)$/.test(unit)) return 'feet';
    return '';
}

function parseItemLine(line) {
    if (ignoredLine.test(line) || !/[a-z]/i.test(line)) return null;

    const unitPattern = new RegExp(`\\b(${units})\\b`, 'i');
    const unitMatch = unitPattern.exec(line);
    if (unitMatch) {
        const before = line.slice(0, unitMatch.index).trim();
        const after = line.slice(unitMatch.index + unitMatch[0].length);
        const quantityMatch = before.match(/(\d+(?:[.,]\d+)?)\s*$/);
        const prices = after.match(/\d[\d,]*(?:\.\d+)?/g) || [];
        if (!quantityMatch || !prices.length) return null;

        let name = cleanName(before.slice(0, quantityMatch.index));
        const hsMatch = name.match(/^(\d{4,10})\s+(.+)$/);
        const trailingHsMatch = name.match(/^(.+?)\s+(\d{4,10})$/);
        const hs_code = hsMatch ? hsMatch[1] : (trailingHsMatch ? trailingHsMatch[2] : null);
        if (hsMatch) name = hsMatch[2].trim();
        else if (trailingHsMatch) name = trailingHsMatch[1].trim();
        if (name.length < 2) return null;
        return {
            name,
            quantity: number(quantityMatch[1]),
            unit: normalizeUnit(unitMatch[1]),
            cost_rate: number(prices[0]),
            sale_price: null,
            hs_code,
        };
    }

    const match = line.match(/^\s*(?:\d+[.)\-]?\s+)?(.+?)\s+(\d+(?:[.,]\d+)?)\s+(\d[\d,]*(?:\.\d+)?)\s+(\d[\d,]*(?:\.\d+)?)\s*$/);
    if (!match) return null;

    let name = cleanName(match[1]);
    const hsMatch = name.match(/^(\d{4,10})\s+(.+)$/);
    const trailingHsMatch = name.match(/^(.+?)\s+(\d{4,10})$/);
    const hs_code = hsMatch ? hsMatch[1] : (trailingHsMatch ? trailingHsMatch[2] : null);
    if (hsMatch) name = hsMatch[2].trim();
    else if (trailingHsMatch) name = trailingHsMatch[1].trim();
    if (name.length < 2) return null;
    return {
        name,
        quantity: number(match[2]),
        unit: 'pcs',
        cost_rate: number(match[3]),
        sale_price: null,
        hs_code,
    };
}

function parseInvoice(text) {
    const lines = String(text || '').split(/\r?\n/).map(line => line.replace(/[|]/g, ' ').replace(/\s+/g, ' ').trim()).filter(Boolean);
    const headerLines = lines.slice(0, 25);
    const joinedHeader = headerLines.join('\n');
    const billMatch = joinedHeader.match(/(?:invoice|bill)\s*(?:no\.?|number|#)?\s*[:#-]?\s*([a-z0-9\/-]+)/i);
    const dateLine = headerLines.find(line => /\bdate\b/i.test(line) && isoDate(line));
    const anyDateLine = lines.find(line => isoDate(line));
    const supplier = headerLines.find(line => /\b(?:pvt\.?\s*ltd|private\s+limited|traders?|suppliers?|enterprises?|distributors?|company|store)\b/i.test(line));
    const items = [];

    lines.forEach(line => {
        const item = parseItemLine(line);
        if (!item || items.length >= 12) return;
        const duplicate = items.some(existing => existing.name.toLowerCase() === item.name.toLowerCase() && existing.quantity === item.quantity);
        if (!duplicate) items.push(item);
    });

    let confidence = 0.25;
    if (billMatch) confidence += 0.2;
    if (dateLine || anyDateLine) confidence += 0.15;
    if (supplier) confidence += 0.15;
    if (items.length) confidence += 0.25;

    return {
        invoice_date: isoDate(dateLine || anyDateLine),
        bill_no: billMatch ? billMatch[1] : null,
        supplier_name: supplier ? supplier.trim() : null,
        notes: null,
        confidence: Math.min(confidence, 1),
        items,
    };
}

async function imageSources(file) {
    if (file.type !== 'application/pdf') return [URL.createObjectURL(file)];

    const pdf = await pdfjs.getDocument({ data: await file.arrayBuffer() }).promise;
    const sources = [];
    const pageCount = Math.min(pdf.numPages, 3);
    for (let pageNumber = 1; pageNumber <= pageCount; pageNumber++) {
        const page = await pdf.getPage(pageNumber);
        const viewport = page.getViewport({ scale: 2 });
        const canvas = document.createElement('canvas');
        canvas.width = viewport.width;
        canvas.height = viewport.height;
        await page.render({ canvasContext: canvas.getContext('2d'), viewport }).promise;
        sources.push(canvas);
    }
    return sources;
}

async function extract(file, onProgress = () => {}) {
    const sources = await imageSources(file);
    let activePage = 0;
    const worker = await createWorker('eng', 1, {
        logger(message) {
            if (message.status === 'recognizing text') {
                const overall = (activePage + (message.progress || 0)) / sources.length;
                onProgress(Math.round(overall * 100));
            }
        },
    });

    const pages = [];
    try {
        for (activePage = 0; activePage < sources.length; activePage++) {
            const result = await worker.recognize(sources[activePage]);
            pages.push(result.data.text || '');
        }
    } finally {
        await worker.terminate();
        sources.forEach(source => {
            if (typeof source === 'string') URL.revokeObjectURL(source);
        });
    }

    return parseInvoice(pages.join('\n'));
}

window.BillOCR = { extract };
