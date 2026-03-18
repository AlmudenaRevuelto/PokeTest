# ![PokeTest avatar](assets/readme/avatar.png) PokeTest

WordPress theme challenge implementation built on Understrap, with custom Pokemon features (custom post types, filters, API pages, REST routes, and TypeScript-powered frontend).

---

## 🛠️ 1. Setup (Run the Project)

### ✅ Requirements
- WordPress installed locally.
- PHP and MySQL compatible with your local WordPress stack.
- Node.js >= 18 and npm >= 8.6.

### 🚀 Installation
1. Place this theme folder in `wp-content/themes/PokeTest`.
2. Activate the theme in WordPress admin.
3. Install dependencies:
```bash
npm install
```
### 🏗️ Build Commands
- Bootstrap 4 CSS bundle:
```bash
npm run css-bs4
```
- TypeScript build:
```bash
npm run build-ts
```
- Optional watch mode:
```bash
npm run watch-ts
```

### ⚠️ Recommended After Route Changes
1. Go to WordPress Admin > Settings > Permalinks.
2. Click Save (no changes required) to flush rewrite rules.

---

## 🌐 2. Main URLs / Endpoints

### 🖥️ Frontend Routes

- **Pokemon API detail (pretty):**  
  `/pokemon-api/{name}/`

- **Pokemon API detail (fallback):**  
  `/?api_pokemon={name}`

- **Random Pokemon post:**  
  `/random/`

- **Generate Pokemon post:**  
  `/generate/`

---

### 🔌 REST API

- **List:**  
  `/wp-json/poketest/v1/pokemon`

- **Detail:**  
  - `/wp-json/poketest/v1/pokemon/{identifier}`  

  - `identifier` supports:
  	- Post ID  
  	- Pokedex ID  
  	- Slug / Name 

---

## 🔍 3. Traceability by Challenge Point (Requirement → Files)

| #  | ⚙️ Feature | 📁 Files / Locations |
|----|----------|---------------------|
| 1  | Custom Pokemon post type | `inc/PostTypes/pokemon-post-type.php` |
| 2  | Pokemon taxonomy and admin meta boxes | `inc/PostTypes/pokemon-post-type.php`, `inc/PostTypes/pokemon-meta-boxes.php`, `src/ts/main.ts` |
| 3  | Pokemon single template (Twig) | `single-pokemon.php`, `views/single-pokemon.twig`, `src/sass/pokemon-card.scss` |
| 4  | Pokemon filter page template + card grid | `page-templates/page-pokemon-filter.php`, `views/page-pokemon-filter.twig`, `src/sass/pokemon-grid.scss`, `src/ts/main.ts` |
| 5  | Pokemon card/grid styles loaded from SCSS sources | `src/sass/pokemon-grid.scss`, `src/sass/pokemon-card.scss`, `src/sass/theme.scss`, `src/sass/theme/_theme.scss` |
| 6  | Front page API Pokedex grid | `front-page.php`, `views/front-page.twig`, `src/ts/main.ts (initPokemonApiGrid)` |
| 7  | Type filter buttons generated from API data | `src/ts/main.ts (initPokemonApiGrid + initPokemonFilter)`, `src/sass/pokemon-grid.scss` |
| 8  | API virtual detail route (works without existing WP post) | `inc/Services/pokemon-api-routes.php`, `single-pokemon-api.php`, `inc/Core/Loader.php` |
| 9  | API service layer for Pokemon data | `inc/Services/pokemon-api-service.php` |
| 10 | Move descriptions fetched from move endpoint (`short_effect`) | `inc/Services/pokemon-api-service.php`, `single-pokemon-api.php`, `inc/Services/pokemon-generate-route.php` |
| 11 | Pokemon species description (`pokemon-species/{id}`) | `inc/Services/pokemon-api-service.php`, `single-pokemon-api.php`, `inc/Services/pokemon-generate-route.php` |
| 12 | Old Pokedex retrieval and UX behavior (button hides after click) | `src/ts/main.ts (initOldPokedexButton)`, `views/single-pokemon.twig`, `inc/Ajax/pokemon-ajax.php` |
| 13 | Pokemon generation route | `inc/Services/pokemon-generate-route.php`, `inc/Services/pokemon-api-service.php` |
| 14 | Random Pokemon route | `inc/Services/pokemon-random-route.php` |
| 15 | Custom REST API routes | `inc/Services/pokemon-rest-routes.php` |
| 16 | Script/style enqueue and frontend localized settings | `inc/enqueue.php` |
| 17 | Header/footer professional polish + endpoint quick links | `header.php`, `footer.php`, `src/sass/theme/_header-footer.scss` |
| 18 | Admin menu icon for Pokemon CPT (custom SVG) | `assets/admin-icons/pokeball-menu.svg`, `inc/Helpers/pokemon-admin-icon.php`, `inc/PostTypes/pokemon-post-type.php`, `inc/Core/Loader.php` |
| 19 | Browser favicon set to Pokeball | `inc/extras.php`, `assets/admin-icons/pokeball-menu.svg` |

---

## 📝 4. Notes / Assets

- 📦 Compiled assets under `css/` and `js/` are generated output; **source of truth is in `src/`**.

- 🔄 If an endpoint/route seems missing:
  - Flush permalinks once  
  - Hard refresh browser cache  

---

## 🧪 5. Tests

Unit and functional tests are included using **PHPUnit 12**.  
Tests are located at:
`wp-content/themes/PokeTest/tests/`


### 📂 Main Test Files

| 🧾 Test | 🔍 What it Verifies |
|--------|--------------------|
| `PokeApiServiceTest.php` | Checks that **PokeApiService** fetches and processes Pokemon data correctly, including moves and description. |
| `PokemonGeneratorTest.php` | Verifies that the `/generate` random Pokemon generator produces valid IDs. |
| `PokemonRandomTest.php` | Ensures `/random` route redirects correctly to an existing Pokemon post. |
| `PokemonRestEndpointTest.php` | Validates REST endpoints return correct Pokemon data stored in WordPress. |

### ▶️ Running Tests

Install PHPUnit (if not globally installed):

```bash
composer require --dev phpunit/phpunit
```
Run all tests from the theme root:
```bash
vendor/bin/phpunit
```

### ✅ Expected Output
```bash
PHPUnit 12.5.14 by Sebastian Bergmann and contributors.

Runtime:       PHP 8.3.6
Configuration: /mnt/c/Users/Usuario/Local Sites/poketest/app/public/wp-content/themes/PokeTest/phpunit.xml

.................                                                 17 / 17 (100%)

Time: 00:00.016, Memory: 14.00 MB

OK (17 tests, 30 assertions)
```

### ⚠️ Note
Ensure `bootstrap.php` points correctly to your WordPress environment to load required functions and classes.

---

## ⚠️ 6. Difficulties / Observations

Challenges encountered during development:

### 🧩 Clean WordPress environment + TypeScript + Twig
- Setting up a theme from scratch with TS and Twig required multiple configuration adjustments.  
- Compiling SCSS and TS correctly for frontend and admin took several iterations.  

### 🛠️ Editing in WordPress without ACF or page builders
- Custom meta boxes were necessary for Pokemon, their types, and moves.  
- This required investigating WordPress-native ways to add custom inputs to admin.  

### 🌐 Integration with PokeAPI for the homepage
- Filters are dynamically generated based on API data.  
- Data is displayed in cards, and navigation/redirection to Pokemon posts happens automatically.  

### 🔌 REST endpoints and custom routes
- Implemented `/random`, `/generate` and `/pokemon-api/{name}` with full support for WordPress permalinks.  

---

## ✨ 7. Additional Implementations

- Homepage fetches Pokemon from **PokeAPI** with type-based filters.  

- Redirection to Pokemon posts from homepage using API data.  

- Header and footer include quick access to `/random` and `/generate`.  

- Custom admin icon for Pokemon custom post type (Pokeball).  

---

## 🏆 8. Challenge Requirements Overview

This section explains how each requested feature in the challenge is implemented and how it can be used within WordPress.

### ![Pokeball icon](assets/readme/pokeball.png) Custom Post Type: Pokemon

Once the theme is activated, a new custom post type called **Pokemon** becomes available in the WordPress admin.

- A custom Pokeball icon has been added to improve visual identification in the admin menu.  
- Users can create new Pokemon posts manually, filling in the required data defined in the challenge.

### 🏷️ Taxonomies (Pokemon Types / Filters)

- A taxonomy has been implemented to represent Pokemon types.  
- These types act as filters across the application.  
- They can be created:  
  - Directly from the WordPress admin, or  
  - While editing a Pokemon post.  
- These taxonomy terms are later used for filtering both in the custom page template and homepage.

### 🔍 Pokemon Detail View

- Each manually created Pokemon post has its own detail page.  
- The template displays all the data entered in the admin.  
- It is recommended to fill in all fields to ensure proper rendering.  
- If the **"Old Pokedex Number"** field is provided:  
  - A button is displayed in the frontend.  
  - When clicked, it reveals the value and then hides itself.

### 📄 Pokemon Filter Page (Template)

- A custom page template called **"Pokemon Filter"** has been created.  
- To use it:  
  1. Go to **Pages** in WordPress admin.  
  2. Create a new page.  
  3. Assign the template **"Pokemon Filter"**.  
  4. Publish the page.

- The page will display:  
  - A grid of manually created Pokemon posts.  
  - A filter based on Pokemon types (taxonomy terms).  

- The filter behavior is consistent with the homepage filter.

### 🏠 Homepage (API Integration)

- The homepage displays Pokemon fetched directly from the **PokeAPI**.  
- It includes:  
  - A grid of Pokemon images.  
  - Dynamic filters based on Pokemon types (fetched from the API).  

- Unlike the filter page:  
  - The homepage uses API data instead of WordPress posts.

### 🎲 Random Pokemon Route (`/random`)

- Accessing `/random` in the browser:  
  - Redirects to a randomly selected published Pokemon post.  
- Quick access links are available in both:  
  - Header  
  - Footer

### ⚡ Generate Pokemon Route (`/generate`)

- Accessing `/generate`:  
  - Fetches a random Pokemon from the API.  
  - Automatically creates a new Pokemon post in WordPress with that data.  
- This action is restricted to users with post creation permissions.  
- A shortcut link is available in the footer.

### 🔌 REST API Endpoints

Two custom REST endpoints have been implemented:

- **List endpoint**  
  `/wp-json/poketest/v1/pokemon`  
  Returns all stored Pokemon.

- **Detail endpoint**  
  `/wp-json/poketest/v1/pokemon/{identifier}`  
  Returns a specific Pokemon by:  
  - Post ID  
  - Pokedex ID  
  - Slug/name

- These endpoints can also be accessed directly from links provided in the footer.

---

## 💡 9. Potential Improvements

- Abstract additional Pokemon APIs (DAPI or others) via service layer.  
- Cache API calls more aggressively.  
- Add integration tests for AJAX & REST routes.  
- Improve accessibility and responsive card layout.  
- Enhance styles for both:
  - Type filters  
  - Pokemon post detail views  

  ---

## 🔹 10. Optional Challenge Questions

### 9 Integration of DAPI or Similar APIs

Yes, integrating **DAPI** (or other Pokemon APIs) is possible within this solution.  
To facilitate this, the following abstractions and changes could be applied:

- **Service Layer Abstraction:** Create an interface for Pokemon data providers. This allows switching between PokeAPI, DAPI, or other sources without changing the rest of the application.  
- **Repository Pattern:** Introduce a repository to fetch Pokemon data, which can call the selected API service or the WordPress database depending on context.  
- **Frontend Flexibility:** Keep the frontend components (Twig templates, JavaScript) unaware of the data source. They would simply render the data passed by the service/repository layer.  
- **Caching Layer:** Cache API responses to reduce redundant calls, improving performance and API rate limit compliance.  

This approach ensures the system remains **modular, maintainable, and easy to extend** with alternative APIs.

### 10 Handling High Traffic and Heavy Database Usage

If the site experiences heavy traffic causing database strain, several strategies can be applied:
 
- **Transient API Data:** Cache external API responses with transients to minimize repeated remote requests.  
- **Static Content / CDN:** Serve images and static assets via a CDN to reduce server load.  
- **Query Optimization:** Review database queries for efficiency, add necessary indexes, and minimize expensive joins.  
- **Pagination & Limits:** Limit the number of items returned in queries (e.g., for filter grids) and paginate large datasets.  
- **Load Balancing / Scaling:** For very high traffic, consider horizontal scaling (multiple WordPress instances) with a shared database or read replicas.  

These measures together improve performance, reduce server stress, and maintain a smooth user experience even under heavy load.