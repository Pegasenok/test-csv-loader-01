<?php


namespace App\Form;


class InputFile extends Input
{
    public function getHtml()
    {
        $name = $this->getName();
        $description = $this->getDescription();
        return "<label for='$name'>$description</label><input type='file' name='$name' />";
    }
}