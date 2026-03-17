// Functions bundled directly in main.ts (avoid import path 404 when se sirve dist/js/main.js sin bundle).

function initPokemonFilter() {
    console.log('Pokemon filter initialized');
}

function initPokemonMoves() {
    const list = document.getElementById('pokemon-moves-list');
    const addBtn = document.querySelector('.add-move');

    if (!list || !addBtn) return;

    // Evitar doble-binding si el script se ejecuta varias veces.
    if ((addBtn as HTMLElement).dataset.pokemonMovesInit === '1') {
        return;
    }
    (addBtn as HTMLElement).dataset.pokemonMovesInit = '1';

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

    list.addEventListener('click', (event) => {
        const target = event.target as HTMLElement;
        if (target.classList.contains('remove-move')) {
            target.closest('li')?.remove();
        }
    });
}

// --- Global declaration for TypeScript ---

declare const wpApiSettings: {
    ajax_url: string;
};

// main.ts
// Entry point for PokeTest frontend

document.addEventListener('DOMContentLoaded', () => {
    initPokemonFilter();
    initPokemonMoves();
    console.log('PokeTest frontend loaded');

    // --- Botón para mostrar Old Pokedex ---
    document.addEventListener('click', (e) => {
        const target = e.target as HTMLElement;

        if (!target || !target.dataset.pokemonId) return;

        const postId = target.dataset.pokemonId;

        if (target.id.startsWith('show-old-pokedex')) {
            fetch(wpApiSettings.ajax_url, {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: `action=get_old_pokedex&post_id=${postId}`
            })
            .then((response) => response.json())
            .then((data) => {
                if (data.success) {
                    const span = document.getElementById(`old-pokedex-${postId}`);
                    if (span) {
                        span.textContent = `${data.data.old_pokedex} (${data.data.game})`;
                    }
                } else {
                    console.error('Error fetching Old Pokedex:', data);
                }
            })
            .catch((err) => console.error('Fetch error:', err));
        }
    });
});