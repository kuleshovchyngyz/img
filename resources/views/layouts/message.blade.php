
@if (session('success_message'))
    <div class="alert alert-success alert-dismissible fade show">

        {{ session('success_message') }}

        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
@elseif (session('warning_message'))
    <div class="alert alert-warning alert-dismissible fade show">
        @foreach (session('warning_message') as $message)
            {{ $message }}
        @endforeach
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
@elseif (session('error_message'))
    <div class="alert alert-danger alert-dismissible fade show">
        @foreach (session('error_message') as $message)
            {{ $message }}
        @endforeach
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
@endif
