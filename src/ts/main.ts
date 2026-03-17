// --- Global declaration for WordPress AJAX ---
declare const wpApiSettings: {
    ajax_url: string;
};

// --- Pokemon Filter ---
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
        button.addEventListener('click', () => {
            currentType = button.dataset.type || 'all';
            currentPage = 1;

            buttons.forEach(b => b.classList.remove('active'));
            button.classList.add('active');

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

// --- Pokemon Moves (Admin) ---
// Handles the dynamic add/remove move UI inside the WordPress post editor meta box.
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

// --- Show Old Pokedex Button ---
// Fetches the old Pokédex number and game name for a pokemon via WordPress AJAX.
// Uses event delegation so it works even if the button is rendered inside a Twig partial.
function initOldPokedexButton() {
    document.addEventListener('click', (e) => {
        const target = e.target as HTMLElement;
        if (!target || !target.dataset.pokemonId) return;

        const postId = target.dataset.pokemonId;
        if (target.id.startsWith('show-old-pokedex')) {
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
                } else {
                    console.error('Error fetching Old Pokedex:', data);
                }
            })
            .catch(err => console.error('Fetch error:', err));
        }
    });
}

// --- DOMContentLoaded ---
document.addEventListener('DOMContentLoaded', () => {
    initPokemonFilter();
    initPokemonMoves();
    initOldPokedexButton();
    console.log('PokeTest frontend loaded');
});