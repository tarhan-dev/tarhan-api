<?php

namespace Tests\Feature\Fields;

use App\Models\Field;
use Tests\TestCase;

class CreateFieldTest extends TestCase {

    #-------------------------------------##   <editor-fold desc="setUp">   ##----------------------------------------------------#

    /**
     * @var $data
     */
    protected $data;


    /**
     * set data property
     *
     * @param array $override
     * @return CreateFieldTest
     */
    protected function setData($override = [])
    {
        $this->data = raw(Field::class, $override);

        return $this;
    }

    /**
     * send the request to store the catalog
     *
     * @return \Illuminate\Foundation\Testing\TestResponse
     */
    protected function store()
    {
        return $this->adminLogin()->postJson(
            route('fields.store'), $this->data
        );
    }

    # </editor-fold>

    #-------------------------------------##   <editor-fold desc="The Security">   ##----------------------------------------------------#

    /** @test */
    public function an_guest_can_not_create_new_field()
    {
        $this->postJson(
            route('fields.store'), []
        )->assertStatus(401);
    }

    /** @test */
    public function an_authenticated_customer_can_not_create_new_field()
    {
        $this->customerLogin()->postJson(
            route('fields.store'), []
        )->assertStatus(401);
    }

    # </editor-fold>

    #-------------------------------------##   <editor-fold desc="The Validation">   ##----------------------------------------------------#

    /** @test */
    public function it_required_the_valid_title_for_field()
    {
        $this->setData(['title' => null])
            ->store()
            ->assertStatus(422)
            ->assertJsonValidationErrors('title');
    }


    /** @test */
    public function it_can_take_the_valid_icon_for_field()
    {
        $this->setData(['icon' => null])
            ->store()
            ->assertJsonMissingValidationErrors('icon');
    }

    # </editor-fold>


    /** @test */
    public function it_store_field_in_database()
    {
        $this->setData()
            ->store()
            ->assertStatus(201)
            ->assertJsonStructure([
                'data', 'message'
            ]);
        $this->assertDatabaseHas(
            'fields', $this->data
        );
    }
}
