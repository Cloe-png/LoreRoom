<?php

namespace Tests\Feature;

use App\Models\Place;
use App\Models\User;
use App\Models\World;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Http\Middleware\EnsureTemporaryLoginToken;
use Tests\TestCase;

class PlaceManagementTest extends TestCase
{
    use RefreshDatabase;

    private $user;
    private $world;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutMiddleware(EnsureTemporaryLoginToken::class);
        $this->user = User::factory()->create();
        $this->world = World::create(['user_id'=>$this->user->id,'name'=>'Aria','slug'=>'aria-'.$this->user->id]);
        $this->user->forceFill(['current_world_id'=>$this->world->id,'login_token_hash'=>hash('sha256','test-token'),'login_token_expires_at'=>now()->addHour()])->save();
        $this->actingAs($this->user)->withSession(['login_temp_token'=>'test-token','selected_world_id'=>$this->world->id]);
    }

    public function test_quick_creation_succeeds_with_three_fields_and_name_is_required(): void
    {
        $this->get(route('manage.places.create'))->assertOk();
        $this->post(route('manage.places.store'), ['name'=>'Valdor','type'=>'ville','short_description'=>'Une cité fortifiée.'])
            ->assertRedirect(route('manage.places.index'));
        $this->assertDatabaseHas('places',['world_id'=>$this->world->id,'name'=>'Valdor']);
        $place = Place::where('world_id',$this->world->id)->where('name','Valdor')->firstOrFail();
        $this->get(route('manage.places.show',$place))->assertOk();
        $this->get(route('manage.places.edit',$place))->assertOk();
        $this->post(route('manage.places.store'), ['type'=>'ville','short_description'=>'Lieu anonyme.'])
            ->assertSessionHasErrors('name');
    }

    public function test_another_worlds_place_cannot_be_read_updated_or_deleted(): void
    {
        $otherUser = User::factory()->create();
        $otherWorld = World::create(['user_id'=>$otherUser->id,'name'=>'Autre','slug'=>'autre-'.$otherUser->id]);
        $foreign = Place::create(['world_id'=>$otherWorld->id,'name'=>'Secret','type'=>'ville','short_description'=>'Privé']);
        $this->get(route('manage.places.show',$foreign))->assertNotFound();
        $this->put(route('manage.places.update',$foreign), ['name'=>'Pris','type'=>'ville','short_description'=>'Intrusion','status'=>'inconnu','visibility'=>'brouillon'])->assertNotFound();
        $this->delete(route('manage.places.destroy',$foreign))->assertNotFound();
    }

    public function test_parent_cycle_is_rejected(): void
    {
        $parent = Place::create(['world_id'=>$this->world->id,'name'=>'Royaume','type'=>'royaume','short_description'=>'Royaume']);
        $child = Place::create(['world_id'=>$this->world->id,'name'=>'Ville','type'=>'ville','short_description'=>'Ville','parent_id'=>$parent->id]);
        $this->put(route('manage.places.update',$parent), ['name'=>'Royaume','type'=>'royaume','short_description'=>'Royaume','parent_id'=>$child->id,'status'=>'inconnu','visibility'=>'brouillon'])
            ->assertSessionHasErrors('parent_id');
    }

    public function test_deleting_parent_reparents_children_to_grandparent(): void
    {
        $grand = Place::create(['world_id'=>$this->world->id,'name'=>'Continent','type'=>'continent','short_description'=>'Continent']);
        $parent = Place::create(['world_id'=>$this->world->id,'name'=>'Royaume','type'=>'royaume','short_description'=>'Royaume','parent_id'=>$grand->id]);
        $child = Place::create(['world_id'=>$this->world->id,'name'=>'Ville','type'=>'ville','short_description'=>'Ville','parent_id'=>$parent->id]);
        $this->delete(route('manage.places.destroy',$parent))->assertRedirect(route('manage.places.index'));
        $this->assertDatabaseHas('places',['id'=>$child->id,'parent_id'=>$grand->id]);
    }

    public function test_private_notes_are_absent_from_world_export(): void
    {
        $place = Place::create(['world_id'=>$this->world->id,'name'=>'Archive','type'=>'autre','short_description'=>'Description','private_notes'=>'SECRET-TEST','status'=>'inconnu','visibility'=>'secret']);
        $controller = app(\App\Http\Controllers\AccountSettingsController::class);
        $method = new \ReflectionMethod($controller, 'buildWorldExportPayload');
        $method->setAccessible(true);
        $payload = $method->invoke($controller, $this->world, now());
        $this->assertStringNotContainsString('SECRET-TEST', json_encode($payload));
    }
}
