document.addEventListener("DOMContentLoaded", () => {
  for (let btn of document.querySelectorAll(".mn-filmmark-ajax-button")) {
    btn.addEventListener("click", (ev) => {
      ev.preventDefault();
      ev.stopPropagation();
      const action = ev.target.dataset.action;
      const film_id = ev.target.dataset.filmid;
      const user_id = ev.target.dataset.userid;
      const query = new URLSearchParams();
      query.set("action", action + "_filmmark");
      query.set("_ajax_nonce", ajax_filmmark.nonce);
      query.set("user_id", user_id);
      query.set("film_id", film_id);

      fetch(ajax_filmmark.ajax_url, {
        method: "POST",
        headers: {
          'Content-Type': 'application/x-www-form-urlencoded; charset=utf-8',
          'Accept': 'application/json; charset=utf-8'
        },
        body: query.toString(),
      }).then(res => res.json())
        .then(data => {
          ev.target.innerText = action === "drop" ? "Guardar" : "Borrar";
          ev.target.dataset.action = action === "drop" ? "save" : "drop";
        });
    }, true);
  }
});
