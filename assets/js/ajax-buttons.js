document.addEventListener("DOMContentLoaded", () => {
  const bindButtons = (root = document) => {
    for (let btn of root.querySelectorAll(".mn-filmmarks__ajax-button")) {
      btn.classList.add("ready");
      btn.addEventListener(
        "click",
        (ev) => {
          ev.preventDefault();
          ev.stopPropagation();
          const action = btn.dataset.action;
          const film_id = btn.dataset.filmid;
          const user_id = btn.dataset.userid;
          const query = new URLSearchParams();
          query.set("action", action + "_filmmark");
          query.set("_ajax_nonce", ajaxFilmmarks.nonce);
          query.set("user_id", user_id);
          query.set("film_id", film_id);

          fetch(ajaxFilmmarks.ajax_url, {
            method: "POST",
            headers: {
              "Content-Type":
                "application/x-www-form-urlencoded; charset=utf-8",
              Accept: "application/json; charset=utf-8",
            },
            body: query.toString(),
          })
            .then((res) => res.json())
            .then((data) => {
              btn.dataset.action = action === "drop" ? "save" : "drop";
              btn.dispatchEvent(
                new CustomEvent("ajax:change", { detail: { action } }),
              );
            });
        },
        true,
      );
    }
  };

  const asyncForms = Array.from(
    document.querySelectorAll(".waf-search-form"),
  ).concat(Array.from(document.querySelectorAll(".waf-filter-form")));

  if (asyncForms.length) {
    asyncForms.forEach((form) =>
      form.addEventListener("waf:render", () => {
        bindButtons();
      }),
    );
  } else {
    bindButtons();
  }
});
