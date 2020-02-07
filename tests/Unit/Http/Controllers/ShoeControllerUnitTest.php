<?php declare(strict_types=1);

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use App\Http\Controllers\ShoeController;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Http\Request;
use App\Shoe;

class ShoeControllerUnitTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    public function setUp(): void
    {
        parent::setUp();

        $this->controller = new ShoeController;

        $this->shoe = factory(\App\Shoe::class)->create();
        
        $this->user = factory(\App\User::class)->create();

        $this->request = new Request(
            [
                'name'        => $this->faker->name,
                'color'       => $this->faker->colorName,
                'size'        => $this->faker->numberBetween($min = 1, $max = 48),
                'description' => $this->faker->text,
                'price'       => rand(500,10000),
                'status'      => 1,
            ]
        );

        $this->requestSearch = new Request(
            [
                'type'          => 'DESC',
                'perPage'       => 10,
                'orderBy'       => 'id',
                'search'        => ''
            ]
        );

    }//end setUp()


    public function testShowSuccessAndNullShoes()
    {
        $this->be($this->user);
        $response = $this->controller->show($this->shoe);
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals($this->shoe->id, json_decode($response->getContent())->data->id);

    }//end testShowSuccessAndNullUser()


    public function testIndexAllParametersSearchColorSuccess()
    {
        $this->be($this->user);
        factory(\App\Shoe::class, 10)->create();
        $shoe = Shoe::first();
        $this->requestSearch['search'] = $this->shoe->name;
        $response = $this->controller->index($this->requestSearch);
        
        $this->assertEquals(200, $response->getStatusCode());
    
    }



    public function testIndexNullParametersSuccess()
    {
        $this->be($this->user);
        factory(\App\Shoe::class, 10)->create();
        $response = $this->controller->index($this->requestSearch);
        $this->assertEquals(200, $response->getStatusCode());

    }

    public function testupdateRequestNull()
    {
        $this->be($this->user);

        $this->request['name']        = null;
        $this->request['color']       = null;
        $this->request['size']        = null;
        $this->request['description'] = null;
        $this->request['price']       = null;
        $this->request['status']      = null;
        $response = $this->controller->update($this->request, $this->shoe);
        $this->assertEquals(422, $response->getStatusCode());
        $this->assertEquals('The Name is required.', json_decode($response->getContent())->error[0]);
        $this->assertEquals('The Color is required.', json_decode($response->getContent())->error[1]);
        $this->assertEquals('The Size is required.', json_decode($response->getContent())->error[2]);
        $this->assertEquals('The Price is required.', json_decode($response->getContent())->error[3]);
    }

    public function testupdateFailNoDifferentValue()
    {
        $this->be($this->user);

        $this->request['name']        = $this->shoe->name;
        $this->request['color']       = $this->shoe->color;
        $this->request['size']        = $this->shoe->size;
        $this->request['description'] = $this->shoe->description;
        $this->request['price']       = $this->shoe->price;
        $this->request['status']      = $this->shoe->status;

        $response = $this->controller->update($this->request, $this->shoe);
        
        $this->assertEquals(422, $response->getStatusCode());
        $this->assertEquals('you must specify at least one different value to update.', json_decode($response->getContent())->error);
    }

    public function testStoreValidationFaild()
    {
        $this->be($this->user);

        $this->request['name']        = null;
        $this->request['color']       = null;
        $this->request['size']        = null;
        $this->request['description'] = null;
        $this->request['price']       = null;
        $this->request['status']      = null;
        
        $response = $this->controller->store($this->request);
        $this->assertEquals(422, $response->getStatusCode());
        $this->assertEquals('The Name is required.', json_decode($response->getContent())->error[0]);
        $this->assertEquals('The Color is required.', json_decode($response->getContent())->error[1]);
        $this->assertEquals('The Size is required.', json_decode($response->getContent())->error[2]);
        $this->assertEquals('The Price is required.', json_decode($response->getContent())->error[3]);
    }


    public function testStoreShoeSuccessfull()
    {
        $this->be($this->user);
        $response = $this->controller->store($this->request);
        $this->assertEquals(201, $response->getStatusCode());
    }

    public function testDeleteSuccessfull()
    {
        $this->be($this->user);
        $response = $this->controller->destroy($this->shoe);

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertSoftDeleted('shoes', ['id' => json_decode($response->getContent())->data->id]);
    }


}//end class
