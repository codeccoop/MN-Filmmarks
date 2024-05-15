document.addEventListener("DOMContentLoaded", () => {
  const modal = document.createElement("div");
  modal.id = "wpct-bm-modal";
  modal.setAttribute("hidden", true);
  document.body.appendChild(modal);
});

document.addEventListener("DOMContentLoaded", () => {
  function closeModal(modal) {
    modal.setAttribute("hidden", true);
    modal.innerHTML = "";
    document.body.removeEventListener("click", onClickOut);
  }

  function onClickOut(ev, modal) {
    if (ev.target !== modal && !modal.contains(ev.target)) {
      closeModal(modal);
    }
  }

  function saveBookmark({ listId, postId, userId, bookMarked }) {
    const action = bookMarked
      ? "wpct_bm_drop_bookmark"
      : "wpct_bm_save_bookmark";
    return doRequest(action, {
      user_id: userId,
      post_id: postId,
      list_id: listId,
    })
      .then((res) => {
        if (!res.ok) {
          throw new Error(res);
        }

        return res.json();
      })
      .then((json) => {
        json.data.action = action;
        return json;
      });
  }

  function doRequest(action, params) {
    const query = new URLSearchParams();
    query.set("action", action);
    query.set("_ajax_nonce", ajaxBookmarks.nonce);

    Object.entries(params).forEach(([k, v]) => {
      query.set(k, v);
    });

    return fetch(ajaxBookmarks.ajax_url, {
      method: "POST",
      headers: {
        "Content-Type": "application/x-www-form-urlencoded; charset=utf-8",
        Accept: "application/json; charset=utf-8",
      },
      body: query.toString(),
    });
  }

  function setupModal({ html, userId, postId, bookMark }) {
    const modal = document.getElementById("wpct-bm-modal");
    modal.innerHTML = html;

    const lists = Array.from(modal.querySelectorAll(".wpct-bm-list"));
    for (const list of lists) {
      list.addEventListener("click", () =>
        saveBookmark({
          listId: list.id,
          postId,
          userId,
          bookMarked: Boolean(list.dataset.bookmarked),
        })
          .then(({ message, data }) => {
            modal.innerHTML = `<p class="wpct-bm-msg success">${message}</p>`;
            return new Promise((res) => {
              setTimeout(() => {
                res(data);
              }, 1000);
            });
          })
          .then((res) => {
            const bookMarked =
              res.action === "wpct_bm_save_bookmark" ? "1" : "";
            list.setAttribute("data-bookmarked", bookMarked);

            const filmBookMarked =
              lists.find((list) => list.getAttribute("data-bookmarked")) !==
              void 0;

            bookMark.setAttribute(
              "data-bookmarked",
              filmBookMarked ? "1" : "0",
            );

            bookMark.dispatchEvent(
              new CustomEvent("ajax:change", { detail: res }),
            );

            closeModal(modal);
          })
          .catch((err) => {
            console.warn(err);
            modal.classList.add("ajax-error");
          }),
      );
    }

    document.body.addEventListener("click", (ev) => onClickOut(ev, modal));
    modal.removeAttribute("hidden");
  }

  const bindBookMarks = (root = document) => {
    for (let bookMark of root.querySelectorAll(".wpct-bm-bookmark")) {
      bookMark.classList.add("ready");
      bookMark.addEventListener(
        "click",
        (ev) => {
          ev.preventDefault();
          ev.stopPropagation();
          const postId = bookMark.dataset.postid;
          const userId = bookMark.dataset.userid;

          doRequest("wpct_bm_user_lists", { user_id: userId, post_id: postId })
            .then((res) => res.text())
            .then((html) => {
              setupModal({ bookMark, html, userId, postId });
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
        bindBookMarks();
      }),
    );
  } else {
    bindBookMarks();
  }
});
