<?php

namespace Tests\Unit\Http\Requests;

use Tests\TestCase;

/**
 * @see \App\Http\Requests\StoreStoreRequest
 */
class StoreStoreRequestTest extends TestCase
{
    /** @var \App\Http\Requests\StoreStoreRequest */
    private $subject;

    protected function setUp() : void
    {
        parent::setUp();

        $this->subject = new \App\Http\Requests\StoreStoreRequest();
    }

    /**
     * @test
     */
    public function authorize()
    {
        $actual = $this->subject->authorize();

        $this->assertTrue($actual);
    }

    /**
     * @test
     */
    public function rules()
    {
        $actual = $this->subject->rules();

        $this->assertValidationRules([
            'name' => [
                'required',
                'unique:stores,name',
            ],
        ], $actual);
    }
}
