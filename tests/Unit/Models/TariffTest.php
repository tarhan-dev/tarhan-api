<?php

namespace Tests\Unit\Models;

use App\Models\Category;
use App\Models\Tariff;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class TariffTest extends TestCase {

    use WithFaker;

    #-------------------------------------##   <editor-fold desc="setUp">   ##----------------------------------------------------#

    /**
     * @var Tariff $tariff
     */
    protected $tariff;

    /**
     * @var Category $category
     */
    protected $category;

    /**
     * Setup the test environment.
     *
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();
        $this->category = create(Category::class);
        $this->tariff = create(Tariff::class, [
            'category_id' => $this->category->id
        ]);
    }

    /** @test */
    public function it_should_extends_base_model()
    {
        $this->assertTrue(
            is_subclass_of(
                $this->tariff, 'App\Models\Model'
            )
        );
    }

    # </editor-fold>

    #-------------------------------------##   <editor-fold desc="The Guarded">   ##----------------------------------------------------#

    /**
     * checking guard data
     *
     * @param array $guardData
     */
    protected function assertGuard(array $guardData)
    {
        $this->tariff->update(
            raw(Tariff::class, $guardData)
        );
        $this->assertDatabaseMissing('tariffs', $guardData);
    }

    /** @test */
    public function it_should_guarded_the_id_field()
    {
        $this->assertGuard(['id' => 14048343]);
    }

    # </editor-fold>

    #-------------------------------------##   <editor-fold desc="The RelationShips">   ##----------------------------------------------------#

    /** @test */
    public function it_belongs_to_a_category()
    {
        $this->assertEquals(
            $this->tariff->category->id, $this->category->id
        );
    }

    # </editor-fold>


}
