// Global declaration for WordPress AJAX settings localized in PHP.
declare const wpApiSettings: {
    ajax_url: string;
    home_url?: string;
};

// Initialize type filtering and client-side pagination for Pokemon grids.
function initPokemonFilter() {
    const buttons = document.querySelectorAll<HTMLButtonElement>('.filter-buttons button');
    const cards = document.querySelectorAll<HTMLDivElement>('.pokemon-grid .pokemon-card');
    const prevBtn = document.querySelector<HTMLButtonElement>('.prev-page');
    const nextBtn = document.querySelector<HTMLButtonElement>('.next-page');

    let currentPage = 1;
    const perPage = 6; // Number of cards shown per page (client-side pagination).
    let currentType = 'all'; // Active type filter; 'all' shows every card.

    // Hides all cards, then shows only the slice for the requested page
    // within the currently filtered set. Also updates prev/next button states.
    function showPage(page: number) {
        // Build the set of cards that match the current type filter.
        // Cards carry a `data-types` attribute with a comma-separated list
        // of all their types, allowing multi-type pokemon to match any filter.
        const filteredCards = Array.from(cards).filter(card => {
            if (currentType === 'all') return true;
            const types = card.dataset.types?.split(',') || [];
            return types.includes(currentType);
        });

        // Hide every card before revealing only the current page's slice.
        cards.forEach(card => (card.style.display = 'none'));

        const start = (page - 1) * perPage;
        const end = start + perPage;
        filteredCards.slice(start, end).forEach(card => (card.style.display = 'block'));

        // Disable navigation buttons at boundaries to prevent invalid pages.
        if (prevBtn) prevBtn.disabled = currentPage <= 1;
        if (nextBtn) nextBtn.disabled = end >= filteredCards.length;
    }

    // When a filter button is clicked, update the active type, reset to page 1,
    // toggle the active class, and re-render the grid.
    buttons.forEach(button => {
        // Avoid attaching duplicate listeners when this initializer runs again.
        if (button.dataset.filterInit === '1') return;
        button.dataset.filterInit = '1';

        button.addEventListener('click', () => {
            currentType = button.dataset.type || 'all';
            currentPage = 1;

            buttons.forEach(b => {
                b.classList.remove('active', 'btn-primary');
                b.classList.add('btn-secondary');
            });
            button.classList.add('active');
            button.classList.remove('btn-secondary');
            button.classList.add('btn-primary');

            showPage(currentPage);
        });
    });

    prevBtn?.addEventListener('click', () => {
        if (currentPage > 1) {
            currentPage--;
            showPage(currentPage);
        }
    });

    nextBtn?.addEventListener('click', () => {
        currentPage++;
        showPage(currentPage);
    });

    // Render the first page on load.
    showPage(currentPage);
}

// Handle the dynamic add/remove move UI in the WordPress post editor meta box.
function initPokemonMoves() {
    const list = document.getElementById('pokemon-moves-list');
    const addBtn = document.querySelector('.add-move');

    // Exit if the required elements are not present on this page.
    if (!list || !addBtn) return;

    // Guard against double-binding if this function is called more than once.
    if ((addBtn as HTMLElement).dataset.pokemonMovesInit === '1') return;
    (addBtn as HTMLElement).dataset.pokemonMovesInit = '1';

    // Append a new move row. The index is derived from the current item count
    // so that the `name` attributes produce a correctly-indexed PHP array.
    addBtn.addEventListener('click', () => {
        const index = list.querySelectorAll(':scope > li').length;
        const li = document.createElement('li');
        li.className = 'pokemon-move-item';

        li.innerHTML = `
            <div class="pokemon-move-inputs">
                <input type="text" name="pokemon_moves[${index}][name]" placeholder="Move Name" />
                <textarea name="pokemon_moves[${index}][description]" placeholder="Move Description"></textarea>
            </div>
            <button type="button" class="button remove-move">Remove</button>
        `;
        list.appendChild(li);
    });

    // Use event delegation on the list so dynamically added rows are covered.
    list.addEventListener('click', (event) => {
        const target = event.target as HTMLElement;
        if (target.classList.contains('remove-move')) {
            target.closest('li')?.remove();
        }
    });
}

// Fetch the old Pokedex number and game name via WordPress AJAX.
// Event delegation is used so it also works with dynamically rendered content.
function initOldPokedexButton() {
    document.addEventListener('click', (e) => {
        const target = e.target as HTMLElement;
        if (!target || !target.dataset.pokemonId) return;

        const postId = target.dataset.pokemonId;
        if (target.id.startsWith('show-old-pokedex')) {
            // If the value was already resolved server-side (e.g. from PokeAPI),
            // it is stored in data-old-pokedex and can be shown without an AJAX call.
            if (target.dataset.oldPokedex) {
                const span = document.getElementById(`old-pokedex-${postId}`);
                if (span) span.textContent = target.dataset.oldPokedex;
                target.remove();
                return;
            }

            // POST to the WordPress AJAX handler registered under the 'get_old_pokedex' action.
            fetch(wpApiSettings.ajax_url, {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: `action=get_old_pokedex&post_id=${postId}`
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Inject the result into the sibling span that holds the display value.
                    const span = document.getElementById(`old-pokedex-${postId}`);
                    if (span) {
                        span.textContent = `${data.data.old_pokedex} (${data.data.game})`;
                    }
                    target.remove();
                } else {
                    console.error('Error fetching Old Pokedex:', data);
                }
            })
            .catch(err => console.error('Fetch error:', err));
        }
    });
}

function initPokemonApiGrid() {
    const container = document.getElementById('pokemon-api-grid');
    const filtersContainer = document.getElementById('pokemon-filters');

    if (!container || !filtersContainer) return;

    // Keep "All" and clear previously generated type buttons/cards when reloading.
    filtersContainer
        .querySelectorAll<HTMLButtonElement>('button[data-type]:not([data-type="all"])')
        .forEach(button => button.remove());
    container.innerHTML = '';

    const minVisibleTypes = 10;
    const pokemonLimit = 151;

    // Load pokemon first, then derive filter buttons from the real types present
    // in those pokemon (instead of from the global /type endpoint).
    fetch(`https://pokeapi.co/api/v2/pokemon?limit=${pokemonLimit}`)
        .then(res => res.json())
        .then(data =>
            Promise.all(
                data.results.map((pokemon: { url: string }) =>
                    fetch(pokemon.url).then(res => res.json())
                )
            )
        )
        .then((pokemonList: any[]) => {
            const typeFrequency = new Map<string, number>();

            pokemonList.forEach((pokeData: any) => {
                const types = pokeData.types.map((t: any) => t.type.name as string);

                types.forEach((type: string) => {
                    typeFrequency.set(type, (typeFrequency.get(type) || 0) + 1);
                });

                const artwork = pokeData.sprites.other['official-artwork'].front_default || pokeData.sprites.front_default;
                const baseUrl = wpApiSettings.home_url || `${window.location.origin}/`;
                // Use query var routing so API detail works even without permalink rewrite refresh.
                const detailUrl = `${baseUrl}?api_pokemon=${encodeURIComponent(pokeData.name)}`;
                const card = document.createElement('div');
                card.className = 'pokemon-card';
                card.dataset.type = types[0] || 'default';
                card.dataset.types = types.join(',');

                card.innerHTML = `
                    <a href="${detailUrl}" class="pokemon-detail-link" aria-label="View ${pokeData.name} details">
                        <div class="pokemon-image">
                            <img src="${artwork}" alt="${pokeData.name}">
                        </div>
                    </a>
                `;

                container.appendChild(card);
            });

            // Use the most frequent types so the first filter set is meaningful.
            const sortedTypes = Array.from(typeFrequency.entries())
                .sort((a, b) => b[1] - a[1])
                .map(([type]) => type);

            const visibleTypes = sortedTypes.slice(0, Math.max(minVisibleTypes, 10));

            visibleTypes.forEach((type: string) => {
                const btn = document.createElement('button');
                btn.textContent = type.charAt(0).toUpperCase() + type.slice(1);
                btn.type = 'button';
                btn.dataset.type = type;
                btn.className = 'btn btn-secondary';
                filtersContainer.appendChild(btn);
            });
        })
        .then(() => {
            initPokemonFilter();
        })
        .catch(err => console.error('Error loading API pokemon grid:', err));
}

// Initialize frontend behaviors once the DOM is ready.
document.addEventListener('DOMContentLoaded', () => {
    // Front page builds cards asynchronously from PokeAPI, so initialize the
    // filter only after cards/buttons exist (inside initPokemonApiGrid).
    if (!document.getElementById('pokemon-api-grid')) {
        initPokemonFilter();
    }
    initPokemonMoves();
    initOldPokedexButton();
    initPokemonApiGrid();
    console.log('PokeTest frontend loaded');
});