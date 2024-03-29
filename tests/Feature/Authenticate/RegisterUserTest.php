<?php

namespace Tests\Feature\Authenticate;

use App\Models\User;
use Tests\TestCase;

class RegisterUserTest extends TestCase {

    #-------------------------------------##   <editor-fold desc="setUp">   ##----------------------------------------------------#

    /**
     * @var $data
     */
    protected $data = [];

    /**
     * set data property
     *
     * @param array $override
     * @return RegisterUserTest
     */
    protected function setData($override = [])
    {
        $this->data = raw(User::class, $override);

        return $this;
    }

    /**
     * send the request to register the user
     *
     * @return \Illuminate\Foundation\Testing\TestResponse
     */
    protected function register()
    {
        return $this->postJson(
            route('customer.register'), $this->data
        );
    }

    # </editor-fold>

    #-------------------------------------##   <editor-fold desc="Security">   ##----------------------------------------------------#

    /** @test */
    public function an_authenticated_customer_can_not_register_again()
    {
        $this->customerLogin()
            ->register()
            ->assertStatus(200)
            ->assertExactJson([
                'message' => 'already authorized ..!'
            ]);
    }

    # </editor-fold>

    #-------------------------------------##   <editor-fold desc="The Validation">   ##----------------------------------------------------#

    /** @test */
    public function it_required_the_valid_mobile_for_user()
    {
        $this->setData(['mobile' => null])
            ->register()
            ->assertStatus(422)
            ->assertJsonValidationErrors('mobile');

        $this->setData(['mobile' => '0913000043'])
            ->register()
            ->assertStatus(422)
            ->assertJsonValidationErrors('mobile');

        $this->setData([
            'mobile' => create(User::class)->mobile
        ])->register()
            ->assertStatus(422)
            ->assertJsonValidationErrors('mobile');
    }

    /** @test */
    public function it_required_the_valid_name_for_user()
    {
        $this->setData(['name' => null])
            ->register()
            ->assertStatus(422)
            ->assertJsonValidationErrors('name');
    }

    # </editor-fold>

    /** @test */
    public function it_store_new_customer_into_database()
    {
        $this->setData()
            ->register()
            ->assertStatus(201)
            ->assertJsonStructure([
                'message'
            ]);

        $this->assertDatabaseHas('users', $this->data);
    }


}
