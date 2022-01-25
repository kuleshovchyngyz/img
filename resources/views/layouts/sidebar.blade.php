<div class="side-bar" id="side-bar">
    <br>
    <ul class="side-bar__ul">
        <li class="side-bar__li s-between">
            <label class="side-bar__label d-flex">
                <input type="radio" class="side-bar__input" name="service" checked value="1">
                <span class="side-bar__li-info">
                            <span>local</span>
                        </span>
            </label>
            <span class="side-bar__li-mintext">Максимальное сжатие</span>
        </li>
        <li class="side-bar__li s-between">
            <label class="side-bar__label d-flex">
                <input type="radio" class="side-bar__input" id="shortpixel" name="service"  value="2">
                <span class="side-bar__li-info">
                            <span>Shortpixel</span>
                            <span class="side-bar__li-mintext">Рекомендуется</span>
                        </span>
            </label>
            <span class="side-bar__li-mintext">Среднее сжатие</span>
        </li>

    </ul>
    <h3 class="side-bar__title">Указать степень сжатия</h3>
    <ul class="side-bar__ul">
        <li class="side-bar__li s-between">
            <label class="side-bar__label d-flex">
                <input type="radio" class="side-bar__input" name="compression" value="1">
                <span class="side-bar__li-info">
                            <span>Lossy</span>
                        </span>
            </label>
            <span class="side-bar__li-mintext">Максимальное сжатие</span>
        </li>
        <li class="side-bar__li s-between">
            <label class="side-bar__label d-flex">
                <input type="radio" class="side-bar__input" name="compression" checked="" value="2">
                <span class="side-bar__li-info">
                            <span>Glossy</span>
                            <span class="side-bar__li-mintext">Рекомендуется</span>
                        </span>
            </label>
            <span class="side-bar__li-mintext">Среднее сжатие</span>
        </li>
        <li class="side-bar__li s-between">
            <label class="side-bar__label d-flex">
                <input type="radio" class="side-bar__input" name="compression" value="0">
                <span class="side-bar__li-info">
                            <span>Lossless</span>
                        </span>
            </label>
            <span class="side-bar__li-mintext">Минимальное сжатие</span>
        </li>
    </ul>
    <h3 class="side-bar__title">Изменение размера</h3>
    <ul class="side-bar__ul">
        <li class="side-bar__li s-between">
            <label class="side-bar__label d-flex">
                <input type="radio" class="side-bar__input" name="size" value="0">
                <span class="side-bar__li-info">
                            <span>Без изменения</span>
                        </span>
            </label>
        </li>
        <li class="side-bar__li s-between">
            <label class="side-bar__label d-flex">
                <input type="radio" class="side-bar__input" name="size" checked="" value="1">
                <span class="side-bar__li-info">
                            <span>Стандарт</span>
                        </span>
            </label>
            <span class="side-bar__li-mintext">1200px <br> по большей стороне</span>
        </li>
        <li class="side-bar__li s-between bb-none randome-size-wrap">
            <label class="side-bar__label d-flex">
                <input type="radio" class="side-bar__input" id="randome-size" name="size" value="random">
                <span class="side-bar__li-info">
                            <span>Произвольно</span>
                        </span>
            </label>
            <div class="side-bar-random d-flex align-items-center justify-content-between active">
                <lable class="side-bar-random-lable">
                    <input type="text" name="width" id="width" class="side-bar-random-input" placeholder="Ширина">px
                </lable>
                <label>ИЛИ</label>
                <lable class="side-bar-random-lable">
                    <input type="text" name="height" id="height" class="side-bar-random-input" placeholder="Высота">px
                </lable>
            </div>
        </li>
    </ul>
</div>
