<?php

namespace Tests\Feature\Api;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AuthTest extends TestCase
{
    // Aquest trait fa que la base de dades es reiniciï (rollback) després de cada test
    // Així no deixem dades escombraria a la base de dades després de fer les proves.
    use RefreshDatabase;

    /**
     * Test: L'usuari es pot registrar correctament.
     */
    public function test_user_can_register()
    {
        // 1. Definim les dades que enviarem
        $payload = [
            'name' => 'Usuari de Prova',
            'email' => 'prova@exemple.cat',
            'password' => '12345678',
            'password_confirmation' => '12345678', // Assumint que cal confirmació
        ];

        // 2. Fem una petició POST a la ruta de registre
        $response = $this->postJson('/api/register', $payload);

        // 3. Comprovem què ens retorna
        $response->assertStatus(201) // 201 Created
                 ->assertJsonStructure([ // Comprovem que ens retorna un token
                     'access_token',
                     'token_type'
                 ]);

        // 4. Verifiquem que realment s'ha guardat a la BD
        $this->assertDatabaseHas('users', [
            'email' => 'prova@exemple.cat',
        ]);
    }

    /**
     * Test: L'usuari es pot loguejar.
     */
    public function test_user_can_login()
    {
        // 1. Creem un usuari directament a la BD per aquesta prova
        $user = User::factory()->create([
            'email' => 'login@exemple.cat',
            'password' => Hash::make('secret123')
        ]);

        // 2. Fem la petició de Login
        $response = $this->postJson('/api/login', [
            'email' => 'login@exemple.cat',
            'password' => 'secret123'
        ]);

        // 3. Verifiquem que ha anat bé (codi 200) i que tenim token
        $response->assertStatus(200)
                 ->assertJsonStructure(['access_token']);
    }

    /**
     * Test: Un endpoint protegit no pot ser accedit sense token
     */
    public function test_cannot_access_protected_route_without_token()
    {
        // La ruta /api/me està protegida pel middleware 'auth:sanctum'
        $response = $this->getJson('/api/me');

        // Ens hauria de retornar 401 Unauthorized (No autoritzat)
        $response->assertStatus(401);
    }
}

/* =====================================================================
 * INSTRUCCIONS PER EXECUTAR AQUESTS TESTS:
 * 1. Obre un terminal a la carpeta 'backend'
 * 2. Assegura't de tenir la configuració a phpunit.xml apuntant a SQLite de memòria 
 *    (generalment ja ve per defecte en Laravel per fer tests més ràpids) o a la teva BD de proves.
 * 3. Executa la comanda: php artisan test
 * =====================================================================
 */
