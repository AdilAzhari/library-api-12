<?php

//test('test can create book', function () {
//    $response = $this->withHeaders([
//        'Accept-Language' => 'en',
//    ])->postJson('/api/v1/books', [
//        'title' => 'Laravel API',
//        'author' => 'John Doe',
//        'publication_year' => 2023,
//        'description' => 'Laravel API',
//    ]);
//
//    $response->assertStatus(201);
//});

test('test can get all books', function () {
    $response = $this->withHeaders([
        'Accept-Language' => 'en',
    ])->get('/api/v1/books');
    $response->assertStatus(200);
});
