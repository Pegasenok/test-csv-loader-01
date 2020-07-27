<?php


namespace App\Model;


class SearchFormModel
{
    public function getSearchHtml()
    {
        return <<<HTML
<div>
<form method="get" action="search">
<label for="q">Введите ФИО или e-mail для поиска:</label>
<input type="text" name="q" />
<button type="submit">Find</button>
</form>
</div>
HTML;
    }
}