    <div class="main">
    <div class="main__top s-between">
        <span class="main__top-text">Не загружено ни одного файла :(</span>

        <input type="text" class="main__top-input" placeholder="Название проекта">
    </div>
        <div class="ibase64">

        </div>
    <div class="relative">
        <form action="{{route('dropzone.store')}}" enctype="multipart/form-data" method="post" class="main__wrap-file d-flex dropzone dz-clickable" id="dropzone">
            @csrf

            <input type="hidden" name="folder_id" id="folder_id" value="{{  uniqid() }}">
            <div class="main__dz-info">
                <span class="main__dz-item">Максимальный размер изображения: 100 Мб</span>
                <span class="main__dz-item">Поддерживаемые форматы: .jpg .png</span>
            </div>
            <div class="dz-message main__upload-btn" data-dz-message="">
                <img src="/img/dorp-icon.png" alt="">
                <span class="main__upload-blue">Загрузите изображения</span>
                <span>или перетащите их в это окно</span>
            </div>
            <div class="main__progress-wrap">
                <div class="main__progress-bar">
                    <div class="main__progress-line" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" style="width: 0%"></div>
                </div>
            </div>
        </form>
    </div>
    <div class="main__footer s-between">
        <div class="main__footer-info">
            <span class="main__footer-text">Загрузите изображения</span>
            <span class="main__footer-progress"></span>
        </div>
        <div class="show-sidebar">Настроить сжатие</div>
        <div class="btn mk start-upload disable">Начать</div>
    </div>
</div>
