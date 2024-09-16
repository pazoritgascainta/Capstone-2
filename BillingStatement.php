<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Document</title>
  <link rel="stylesheet" href="BillingStatement.css" type="text/css" media="all" />
</head>

<body>
  <div>
    <div class="py-4">
      <div class="px-14 py-6">
        <table class="w-full border-collapse border-spacing-0">
          <tbody>
            <tr>
              <td class="w-full align-top">
                <div>
                  <img src="monique logo.jpg" width="248" height="360" class="h-12" />
                </div>
              </td>

              <td class="align-top">
                <div class="text-sm">
                  <table class="border-collapse border-spacing-0">
                    <tbody>
                      <tr>
                        <td class="border-r pr-4">
                          <div>
                            <p class="whitespace-nowrap text-slate-400 text-right">Date</p>
                            <p class="whitespace-nowrap font-bold text-main text-right" id="currentDate"></p>
                          </div>
                        </td>
                        <td class="pl-4">
                          <div>
                            <p class="whitespace-nowrap text-slate-400 text-right">Invoice #</p>
                            <p class="whitespace-nowrap font-bold text-main text-right" id="invoiceNumber"></p>
                          </div>
                        </td>
                      </tr>
                    </tbody>
                  </table>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <div class="bg-slate-100 px-14 py-6 text-sm">
        <table class="w-full border-collapse border-spacing-0">
          <tbody>
            <tr>
              <td class="w-1/2 align-top">
                <div class="text-sm text-neutral-600">
                  <p class="font-bold">St Monique Valais</p>
                  <p>Number: 23456789</p>
                  <p>6622 Abshire Mills</p>
                  <p>Binangonan, Rizal</p>
                  <p>Philippines</p>
                </div>
              </td>
              <td class="w-1/2 align-top text-right">
                <div class="text-sm text-neutral-600" id="homeownerDetails">
                  <p class="font-bold">Loading...</p>
                  <!-- Homeowner details will be fetched here -->
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <div class="px-14 py-10 text-sm text-neutral-700">
        <table class="w-full border-collapse border-spacing-0">
          <thead>
            <tr>
              <td class="border-b-2 border-main pb-3 pl-3 font-bold text-main">#</td>
              <td class="border-b-2 border-main pb-3 pl-2 font-bold text-main">Product details</td>
              <td class="border-b-2 border-main pb-3 pl-2 text-right font-bold text-main">Price</td>
              <td class="border-b-2 border-main pb-3 pl-2 text-center font-bold text-main"># of Months</td>
              <td class="border-b-2 border-main pb-3 pl-2 text-right font-bold text-main">Subtotal</td>
            </tr>
          </thead>
          <tbody>
            <tr>
              <td class="border-b py-3 pl-3">1.</td>
              <td class="border-b py-3 pl-2">Monthly Dues</td>
              <td class="border-b py-3 pl-2 text-right">₱ 357.00</td>
              <td class="border-b py-3 pl-2 text-center">8</td>
              <td class="border-b py-3 pl-2 text-right">₱ 2856.00</td>
            </tr>
            <tr>
              <td colspan="7">
                <table class="w-full border-collapse border-spacing-0">
                  <tbody>
                    <tr>
                      <td class="w-full"></td>
                      <td>
                        <table class="w-full border-collapse border-spacing-0">
                          <tbody>
                            <tr>
                              <td class="border-b p-3">
                                <div class="whitespace-nowrap text-slate-400">Net total:</div>
                              </td>
                              <td class="border-b p-3 text-right">
                                <div class="whitespace-nowrap font-bold text-main">₱ 2856.00</div>
                              </td>
                            </tr>
                            <tr>
                              <td class="bg-main p-3">
                                <div class="whitespace-nowrap font-bold text-white">Total:</div>
                              </td>
                              <td class="bg-main p-3 text-right">
                                <div class="whitespace-nowrap font-bold text-white">₱ 2856.00</div>
                              </td>
                            </tr>
                          </tbody>
                        </table>
                      </td>
                    </tr>
                  </tbody>
                </table>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <div class="px-14 text-sm text-neutral-700">
        <p class="text-main font-bold">PAYMENT DETAILS</p>
        <p>Gcash</p>
        <p>Payment Reference: <span id="paymentReference"></span></p>
      </div>

      <div class="px-14 py-10 text-sm text-neutral-700">
        <p class="text-main font-bold">Notes</p>
        <p class="italic">DISREGARD THIS BILLING IF PAYMENT HAS BEEN MADE</p>
      </div>

      <footer class="fixed bottom-0 left-0 bg-slate-100 w-full text-neutral-600 text-center text-xs py-3">
        St Monique Valais Homeowners Association Inc
        <span class="text-slate-300 px-2">|</span>
        StMonique@gmail.com
        <span class="text-slate-300 px-2">|</span>
        Tel No 806-7587
      </footer>
    </div>
  </div>

  <!-- JavaScript for Real-time Date, Random Invoice Number, and Fetch API -->
  <script>
    // Set Current Date
    document.getElementById('currentDate').textContent = new Date().toLocaleDateString();

    // Generate Random Invoice and Payment Reference Numbers
    function generateRandomNumber(prefix, length) {
      let randomNum = Math.floor(Math.random() * Math.pow(10, length));
      return prefix + randomNum.toString().padStart(length, '0');
    }

    document.getElementById('invoiceNumber').textContent = generateRandomNumber('GCH-', 5);
    document.getElementById('paymentReference').textContent = generateRandomNumber('GCH-', 5);

    // Fetch Homeowner Data (assuming API returns JSON with name, number, address)
    async function fetchHomeownerDetails() {
      try {
        const response = await fetch('/getHomeownerDetails');
        const data = await response.json();

        // Dynamically update the homeowner details
        const homeownerDetails = `
          <p class="font-bold">${data.name}</p>
          <p>Number: ${data.phone}</p>
          <p>${data.address.street}, ${data.address.city}</p>
          <p>${data.address.country}</p>
        `;
        document.getElementById('homeownerDetails').innerHTML = homeownerDetails;
      } catch (error) {
        console.error('Error fetching homeowner details:', error);
      }
    }

    fetchHomeownerDetails();
  </script>

</body>

</html>
