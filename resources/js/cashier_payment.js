export const formatRiel = (value) =>
    `៛${Math.max(0, Math.floor(value)).toLocaleString("en-US")}`;

const formatUsd = (value) =>
    new Intl.NumberFormat("en-US", { style: "currency", currency: "USD" }).format(value);

export function paymentDraft(scope) {
    const form = scope?.querySelector(".js-place-order-form");
    if (!form) return null;
    const fields = ["amount_received", "amount_received_khr", "change_currency", "payment_currency", "payment_reference"];
    const draft = Object.fromEntries(fields.map((name) => [name, form.elements.namedItem(name)?.value || ""]));
    draft.payment_method = form.querySelector('[name="payment_method"]:checked')?.value || "cash";
    return draft;
}

export function initPaymentForm(scope = document, draft = null) {
    const form = scope.querySelector(".js-place-order-form");
    if (!form || form.dataset.paymentBound === "1") return;
    form.dataset.paymentBound = "1";

    const totalCents = Math.round(Number(form.dataset.orderTotal) * 100);
    const rate = Number(form.dataset.exchangeRate);
    const empty = form.dataset.cartEmpty === "1";
    const cashPanel = form.querySelector("[data-cash-panel]");
    const digitalPanel = form.querySelector("[data-digital-panel]");
    const usdInput = form.querySelector("[data-cash-usd]");
    const khrInput = form.querySelector("[data-cash-khr]");
    const changeCurrency = form.querySelector("[data-change-currency]");
    const paymentCurrency = form.querySelector("[data-payment-currency]");
    const reference = form.querySelector("[data-payment-reference]");
    const confirmed = form.querySelector("[data-payment-confirmed]");
    const submit = form.querySelector("[data-place-order]");
    const label = form.querySelector("[data-place-order-label]");

    if (draft && !empty) {
        for (const [name, value] of Object.entries(draft)) {
            if (name === "payment_method") {
                const option = [...form.querySelectorAll('[name="payment_method"]')].find((input) => input.value === value);
                if (option) option.checked = true;
            } else if (form.elements.namedItem(name)) {
                form.elements.namedItem(name).value = value;
            }
        }
    }

    const sync = () => {
        const method = form.querySelector('[name="payment_method"]:checked')?.value || "cash";
        const cash = method === "cash";
        cashPanel.hidden = !cash;
        cashPanel.disabled = !cash;
        digitalPanel.hidden = cash;
        digitalPanel.disabled = cash;
        form.querySelector("[data-khqr-panel]").hidden = method !== "qr";
        form.querySelector("[data-card-panel]").hidden = method !== "card";
        let ready = false;

        if (cash) {
            const usdCents = Math.round(Math.max(0, Number(usdInput.value) || 0) * 100);
            const riel = Math.max(0, Number(khrInput.value) || 0);
            const received = usdCents * rate + riel * 100;
            const due = totalCents * rate;
            const change = Math.max(0, received - due);
            const changeUsd = Math.floor((change + Math.floor(rate / 2)) / rate) / 100;
            const changeKhr = Math.floor(change / 100);
            const status = form.querySelector("[data-cash-status]");
            ready = received >= due && usdInput.validity.valid && khrInput.validity.valid;
            form.querySelector("[data-cash-equivalent]").textContent = formatUsd(Math.floor((received + Math.floor(rate / 2)) / rate) / 100);
            form.querySelector("[data-change-amount]").textContent = changeCurrency.value === "KHR" ? formatRiel(changeKhr) : formatUsd(changeUsd);
            status.classList.toggle("text-emerald-700", ready && !empty);
            status.classList.toggle("text-[#8a7265]", !ready || empty);
            if (!empty && totalCents === 0) {
                status.textContent = "No payment is due. You can complete this order.";
            } else if (empty || received === 0) {
                status.textContent = "Enter the customer's cash to continue.";
            } else if (!usdInput.validity.valid || !khrInput.validity.valid) {
                status.textContent = "Use USD with up to 2 decimals and whole riel amounts.";
            } else if (!ready) {
                status.textContent = `Still due: ${formatUsd(Math.ceil((due - received) / rate) / 100)} / ${formatRiel(Math.ceil((due - received) / 100))}`;
            } else {
                const equivalent = changeCurrency.value === "KHR" ? formatUsd(changeUsd) : formatRiel(changeKhr);
                status.textContent = change > 0 ? `Return change in ${changeCurrency.value}. Equivalent: ${equivalent}.` : "Exact amount received. No change needed.";
            }
        } else {
            const currency = paymentCurrency.value;
            form.querySelector("[data-digital-amount]").textContent = currency === "KHR" ? formatRiel(Math.ceil(totalCents * rate / 100)) : formatUsd(totalCents / 100);
            form.querySelectorAll("[data-qr-currency]").forEach((panel) => { panel.hidden = panel.dataset.qrCurrency !== currency; });
            ready = reference.value.trim().length > 0 && reference.validity.valid && confirmed.checked;
        }

        submit.disabled = empty || !ready || form.dataset.submitting === "1";
        if (label && form.dataset.submitting !== "1") label.textContent = cash ? "Complete cash sale" : "Confirm payment & place order";
    };

    form.addEventListener("input", (event) => {
        if (event.target === reference) confirmed.checked = false;
        sync();
    });
    form.addEventListener("change", (event) => {
        if (event.target.name === "payment_method" || event.target === paymentCurrency) confirmed.checked = false;
        sync();
    });
    form.addEventListener("payment:refresh", sync);
    form.addEventListener("click", (event) => {
        const exact = event.target.closest("[data-exact]");
        const quick = event.target.closest("[data-cash-quick]");
        if (exact) {
            usdInput.value = exact.dataset.exact === "USD" ? (totalCents / 100).toFixed(2) : "";
            khrInput.value = exact.dataset.exact === "KHR" ? String(Math.ceil(totalCents * rate / 100)) : "";
            sync();
        } else if (quick) {
            const input = quick.dataset.cashQuick === "USD" ? usdInput : khrInput;
            input.value = quick.dataset.amount;
            sync();
        }
    });
    sync();
}
