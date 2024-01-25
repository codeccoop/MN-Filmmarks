document.addEventListener("DOMContentLoaded", () => {
    for (let btn of document.querySelectorAll(".mn-filmmark-save-button")) {
        btn.addEventListener("click", (ev) => {
            const film_id = ev.target.dataset.film_id;
            const user_id = ev.target.dataset.user_id;
            fetch(_wp_ajax.url, {
                method: "GET",
                nonce: _wp_ajax.nonce,
                action: "save-filmmark",
                user_id,
                film_id
            });
        });
    }
})