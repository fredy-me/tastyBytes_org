(() => {
  function initRepeater(root) {
    const list = root.querySelector("[data-list]");
    const addBtn = root.querySelector("[data-add]");
    if (!list || !addBtn) return;

    const placeholder = root.getAttribute("data-repeater") === "steps" ? "Step" : "Ingredient";
    const fieldName = root.getAttribute("data-repeater") === "steps" ? "steps[]" : "ingredients[]";

    function renumber() {
      const inputs = list.querySelectorAll("input");
      inputs.forEach((input, idx) => {
        input.setAttribute("placeholder", `${placeholder} ${idx + 1}`);
      });
    }

    function addRow(value = "") {
      const row = document.createElement("div");
      row.className = "tb-repeater__row";

      const input = document.createElement("input");
      input.className = "tb-field__input";
      input.type = "text";
      input.name = fieldName;
      input.value = value;

      const remove = document.createElement("button");
      remove.className = "tb-iconbtn";
      remove.type = "button";
      remove.setAttribute("data-remove", "");
      remove.setAttribute("aria-label", `Remove ${placeholder.toLowerCase()}`);
      remove.textContent = "×";

      remove.addEventListener("click", () => {
        row.remove();
        if (list.querySelectorAll(".tb-repeater__row").length === 0) {
          addRow("");
        }
        renumber();
      });

      row.appendChild(input);
      row.appendChild(remove);
      list.appendChild(row);
      renumber();
      input.focus();
    }

    addBtn.addEventListener("click", () => addRow(""));

    list.querySelectorAll("[data-remove]").forEach((btn) => {
      btn.addEventListener("click", () => {
        const row = btn.closest(".tb-repeater__row");
        if (row) row.remove();
        if (list.querySelectorAll(".tb-repeater__row").length === 0) addRow("");
        renumber();
      });
    });

    renumber();
  }

  document.querySelectorAll("[data-repeater]").forEach(initRepeater);
})();

