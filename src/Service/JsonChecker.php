<?php


namespace App\Service;


class JsonChecker
{

    public function checkJson ($json)
    {
        try {

            return $this->json($products, 200, []);

        } catch (NotEncodableValueException $e) {
            return $this->json(
                [
                    'status' => 400,
                    'message' => $e->getMessage(),
                ],
                400,

            );

        }
    }

}