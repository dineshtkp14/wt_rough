@section('breadcrumb')

    <div class="mainbreadcrumb pt-4 pb-4">
        <h5 class="">{{$breadcrumb['subtitle']}} </h5>
     <h1 class=""> {{$breadcrumb['title']}}</h1>
     <nav style="--bs-breadcrumb-divider: '>';" aria-label="breadcrumb">
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="{{url('/')}}"><i class="fa-solid fa-house px-2"></i>Home</a></li>
          <li class="breadcrumb-item active" aria-current="page">{{$breadcrumb['link']}}</li>
        </ol>
      </nav>
    </div>
@endsection