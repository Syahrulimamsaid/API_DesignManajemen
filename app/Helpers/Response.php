<?php

namespace App\Response;

class Response
{
    public function Response($response){
        return response()->json($response);
    }
}
?>