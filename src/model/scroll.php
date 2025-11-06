<?php
class Scroll {
    public string $header;
    public string $content;
    public bool $isOpen;

    public function __construct($header, $content) {
        $this->header = $header;
        $this->content = $content;
        $this->isOpen = false;
    }
}?>