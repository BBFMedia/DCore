<?php


class :hello:layout extends :ui:root {
    // attribute 
    //children (:x:element*)
    //category %catname
    function render()
    {
    return 
        <html>
        <head>
        </head>
     <body>
    {$this->getChildren()}
        </body>
    </html>;
    }
}