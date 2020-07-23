<?php


namespace App\Form;


class UploadFormBuilder
{
    /**
     * @var Input[]
     */
    protected array $inputs;

    /** @var string */
    protected string $name;

    public function addInputFile(string $name, string $description)
    {
        $input = new InputFile();
        $input->setName($name);
        $input->setDescription($description);
        $this->inputs[] = $input;
    }

    public function getHtml()
    {
        $result = sprintf('<form method="post" enctype="multipart/form-data" name="%s" action="%s">', $this->name, $this->name);
        foreach ($this->inputs as $input) {
            $result .= $input->getHtml();
        }
        $result .= '<button type="submit">Send</button></form>';
        return $result;
    }

    public function setFormName(string $name)
    {
        $this->name = $name;
    }
}