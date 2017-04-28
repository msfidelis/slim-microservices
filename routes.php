<?php

$app->group('/v1', function() {

    $this->group('/book', function() {

        $this->get('', '\App\Controllers\v1\BooksController:listBook');
        $this->post('', '\App\Controllers\v1\BooksController:createBook');

        $this->get('/{id:[0-9]+}', '\App\Controllers\v1\BooksController:viewBook');
        $this->put('/{id:[0-9]+}', '\App\Controllers\v1\BooksController:updateBook');
        $this->delete('/{id:[0-9]+}', '\App\Controllers\v1\BooksController:deleteBook');

    });

});