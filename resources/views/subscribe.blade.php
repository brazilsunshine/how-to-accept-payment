<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            Subscribe
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-300 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <div class="p-6">
                        <!-- Display a payment form -->
                        <form
                            id="payment-form"
                            method="POST"
                            action="{{ route('subscribe.post') }}"
                            data-secret="{{ $intent->client_secret }}"
                        >
                            @csrf
                            <div class="mt-4 mb-2 text-black">
                                <input
                                    type="radio"
                                    name="plan"
                                    id="standard"
                                    value="price_1MjN54KfQxRkIXlXj9YsMwJt"
                                    checked
                                >
                                <label for="standard">Standard - $10 / Month</label>

                                <input
                                    type="radio"
                                    name="plan"
                                    id="standard"
                                    value="price_1MjN54KfQxRkIXlXaZrg7KO8"
                                >
                                <label for="standard">Standard - $20 / Month</label>
                            </div>
                            <div id="link-authentication-element">
                                <!--Stripe.js injects the Link Authentication Element-->
                            </div>
                            <div id="payment-element">
                                <!--Stripe.js injects the Payment Element-->
                            </div>
                            <button id="btnsubmit" class="bg-black text-white px-4 py-2 rounded">
                                <div class="spinner hidden" id="spinner"></div>
                                <span id="button-text">Pay now</span>
                            </button>
                            <div id="payment-message" class="hidden"></div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @push('scripts')
        <script src="https://js.stripe.com/v3/"></script>

        <script>
            // This is your test publishable API key.
            const stripe = Stripe("pk_test_51MWf28KfQxRkIXlXqkN0uipSjX7SdJ0gA4bwe23NJAfTwnwfVTNMLt0H3qzCV9oI3q240IFoxusnwKAbzDlLT0q100a6qWZDQg");

            let elements;

            // call to initialize method
            initialize();

            /**
             * EventListener for the handleSubmit()
             */
            document
                .querySelector("#payment-form")
                .addEventListener("submit", handleSubmit);

            /**
             * Initialize stripe
             */
            function initialize() {

                elements = stripe.elements({
                    clientSecret: "{{ $intent->client_secret }}"
                });

                const paymentElement = elements.create("payment");
                paymentElement.mount("#payment-element");
            }

            /**
             * Handle the form
             */
            async function handleSubmit(e) {
                e.preventDefault();

                const {
                    setupIntent,
                    error
                } = await stripe.confirmSetup({
                    elements,
                    confirmParams: {
                        // Make sure to change this to your payment completion page
                        return_url: "http://localhost:4242/checkout.html",
                        //receipt_email: emailAddress,
                    },
                    redirect: "if_required"
                });

                // This point will only be reached if there is an immediate error when
                // confirming the payment. Otherwise, your customer will be redirected to
                // your `return_url`. For some payment methods like iDEAL, your customer will
                // be redirected to an intermediate site first to authorize the payment, then
                // redirected to the `return_url`.

                if (error)
                {
                    if (error.type === "card_error" || error.type === "validation_error") {
                        showMessage(error.message);
                    } else {
                        showMessage("An unexpected error occurred.");
                    }
                } else {
                    console.log({setupIntent});

                    var form = document.getElementById('payment-form');
                    var hiddenInput = document.createElement('input'); // creates a new HTML input element and assigns it to a JavaScript variable named `hiddenInput`
                    hiddenInput.setAttribute('type', 'hidden'); // sets the type attribute of the `hiddenInput` element to "hidden". This means that the element will not be visible on the page.
                    hiddenInput.setAttribute('name', 'paymentMethod'); // sets the input's `name` attribute to "paymentMethod"
                    hiddenInput.setAttribute('value', setupIntent.payment_method); // and its `value` attribute to the ID of a Stripe PaymentMethod object
                    form.appendChild(hiddenInput); // This line appends the hiddenInput element as a child of the form element. This means that when the form is submitted to the server, the paymentMethod parameter will be included with the value of the PaymentMethod ID, allowing the server to associate the payment with the correct PaymentMethod object.

                    // Submit the form
                    form.submit();
                }
            }

            /**
             * UI Helpers
             */
            function showMessage(messageText) {
                const messageContainer = document.querySelector("#payment-message");

                messageContainer.classList.remove("hidden");
                messageContainer.textContent = messageText;

                setTimeout(function () {
                    messageContainer.classList.add("hidden");
                    messageText.textContent = "";
                }, 4000);
            }
        </script>
    @endpush
</x-app-layout>
