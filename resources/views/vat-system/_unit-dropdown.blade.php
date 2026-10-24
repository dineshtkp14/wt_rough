<script>
document.addEventListener('DOMContentLoaded', function () {
    const units = ['PCS', 'KG', 'FEET', 'MTR'];

    function makeUnitDropdown(input) {
        if (!input || input.dataset.unitDropdownReady || input.type === 'hidden') return;
        input.dataset.unitDropdownReady = '1';
        const value = String(input.value || 'PCS').trim().toUpperCase();
        const select = document.createElement('select');
        select.className = 'form-select unit-select';
        select.setAttribute('aria-label', 'Unit');
        units.forEach(unit => select.add(new Option(unit, unit)));
        if (value && !units.includes(value)) select.add(new Option(value, value));
        select.add(new Option('+ Add More...', '__custom__'));
        select.value = value || 'PCS';
        input.type = 'hidden';
        input.value = select.value;
        input.parentNode.insertBefore(select, input);
        select.addEventListener('change', function () {
            if (select.value === '__custom__') {
                const custom = (window.prompt('Enter a unit name, for example: Dozen') || '').trim().toUpperCase();
                if (!custom) { select.value = input.value || 'PCS'; return; }
                select.insertBefore(new Option(custom, custom), select.lastElementChild);
                select.value = custom;
            }
            input.value = select.value;
        });
    }

    function scanUnits(root) {
        (root || document).querySelectorAll('input[name$="[unit]"], input[name="unit"]').forEach(makeUnitDropdown);
    }

    scanUnits(document);
    new MutationObserver(mutations => mutations.forEach(mutation => mutation.addedNodes.forEach(node => {
        if (node.nodeType === 1) scanUnits(node);
    }))).observe(document.body, { childList: true, subtree: true });
});
</script>
