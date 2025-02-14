# TDD Laravel

## CRUD completo

1. El Test:
   1. Generar:

      ```bash
      php artisan make:test RestaurantTest
      ```

   2. Implementar 'Happy Path':

      ```php
      /**
      * Crear una restaurante
      */
      public function test_an_user_can_create_a_restaurant(): void
      {
          # Teniendo
          $data = [
              'name' => 'New restaurant',
              'description' => 'New restaurant description',
          ];

          # Haciendo
          $response = $this->apiAs(User::find(1), 'POST', "{$this->apiBase}/restaurants", $data);

          # Esperando
          $response->assertStatus(200);
          $response->assertJsonStructure([
              'message',
              'data' => ['restaurant' => ['id', 'name', 'slug', 'description']],
              'errors',
              'status'
          ]);

          $this->assertDatabaseCount("restaurants", 1);
          $restaurant = Restaurant::first();
          $this->assertStringContainsString('new-restaurant', $restaurant->slug);
          $this->assertDatabaseHas('restaurants', [
              'id' => 1,
              'user_id' => 1,
              ...$data
          ]);
      }
      ```

   3. Ejecutar:

      ```bash
      # Todos los tests existentes
      php artisan test

      # Todos los tests implementados
      php artisan test RestaurantTest
      
      # Test espeficifico
      php artisan test test_create_a_restaurant
      ```

2. La Generación de Implementaciones API:

    ```bash
    php artisan make:model Restaurant -a --api
    php artisan make:resource RestaurantResource
    ```

3. La Ruta API:

    ```php
    Route::middleware('auth:api')->group(function () {
      Route::apiResource('/restaurants', RestaurantController::class);

      Route::middleware('can:view,restaurant') 
        ->prefix('restaurants/{restaurant:id}')
        ->as('restaurants')
        ->group(function () {
            Route::apiResource('/plates', PlateController::class);
        });
    });
    ```

4. La Migración:

    ```php
    public function up(): void
    {
        Schema::create('restaurants', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description');
            $table->timestamps();
        });
    }
    ```

5. El Modelo:
   1. Asignar campos protegidos:

      ```php
      // Si el controlador usa `$request->validated()`
      protected $guarded = [];

      // Si el controlador usa `$request->all()` <--- (NO RECOMENDADO)
      protected $fillable = [
        'name',
        'description',
      ];
      ```

   2. Las relaciones:

      ```php
      class Restaurant extends Model
      {
          use HasFactory;

          protected $guarded = [];

          public function plates()
          {
              return $this->hasMany(Plate::class);
          }

          public function menus()
          {
              return $this->hasMany(Menu::class);
          }
      }
      ```

6. La Fábrica:

    ```php
    public function definition(): array
    {
        $title = $this->faker->words(3, true);
        return [
            'user_id' => fn() => User::factory()->create(),
            'name' => $title,
            'slug' => str($title)->slug(),
            'description' => $this->faker->text()
        ];
    }
    ```

7. Los Recursos:

    ```php
    // Resource
    class RestaurantResource extends JsonResource
    {
        public function toArray(Request $request): array
        {
            return [
                "id" => $this->id,
                "name" => $this->name,
                "slug" => $this->slug,
                "description" => $this->description,
            ];
        }
    }

    // Collection
    class RestaurantCollection extends ResourceCollection
    {
        public static $wrap = "restaurants";
    }
    ```

8. El Controlador:

    ```php
    class RestaurantController extends Controller
    {
        /**
        *Display a listing of the resource.
        */
        public function index()
        {
            $restaurants = auth()->user()->restaurants()->paginate();
            return jsonResponse(new RestaurantCollection($restaurants));
        }

        /**
        * Store a newly created resource in storage.
        */
        public function store(StoreRestaurantRequest $request)
        {
            $restaurant = auth()->user()->restaurants()->create($request->validated());
            return jsonResponse(data: [
                'restaurant' => RestaurantResource::make($restaurant)
            ]);
        }

        /**
        * Display the specified resource.
        */
        public function show(Restaurant $restaurant)
        {
            Gate::authorize('view', $restaurant);
            return jsonResponse([
                'restaurant' => RestaurantResource::make($restaurant)
            ]);
        }

        /**
        * Update the specified resource in storage.
        */
        public function update(UpdateRestaurantRequest $request, Restaurant $restaurant)
        {
            Gate::authorize('update', $restaurant);
            $restaurant->update($request->validated());
            return jsonResponse(data: [
                'restaurant' => RestaurantResource::make($restaurant)
            ]);
        }

        /**
        * Remove the specified resource from storage.
        */
        public function destroy(Restaurant $restaurant)
        {
            Gate::authorize('delete', $restaurant);
            $restaurant->delete();
            return jsonResponse();
        }
    }
    ```

9. Los Tests de validaciones:

    ```php
    /**
     * El nombre del restaurante es requerido
     */
    public function test_restaurant_name_field_must_be_required(): void
    {
        # Teniendo
        $data = [
            'name' => '',
            'description' => 'New restaurant description',
        ];

        # Haciendo
        $response = $this->apiAs(User::find(1), 'POST', "{$this->apiBase}/restaurants", $data);

        # Esperando
        $response->assertStatus(422);
        $response->assertJsonStructure(['message', 'data', 'status', 'errors' => ['name']]);
    }

    /**
     * El nombre del restaurante es requerido
     */
    public function test_restaurant_name_field_must_have_at_lease_3_characters(): void
    {
        # Teniendo
        $data = [
            'name' => 'ne',
            'description' => 'New restaurant description',
        ];

        # Haciendo
        $response = $this->apiAs(User::find(1), 'POST', "{$this->apiBase}/restaurants", $data);

        # Esperando
        $response->assertStatus(422);
        $response->assertJsonStructure(['message', 'data', 'status', 'errors' => ['name']]);
    }
    ```
