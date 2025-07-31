

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CTV Campaign Sign-up Form</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/faker/5.5.3/faker.min.js"></script>
</head>
<body class="bg-gray-100">
    <div class="min-h-screen px-4 py-12 sm:px-6 lg:px-8">
        <div class="max-w-3xl mx-auto">
            <div class="p-8 bg-white rounded-lg shadow-lg">
                <div class="mb-8 text-center">
                    <h1 class="mb-2 text-3xl font-bold text-gray-900">CTV Campaign Sign-up</h1>
                    <p class="text-gray-600">Start your Connected TV advertising journey today</p>
                </div>

                <form id="ctvForm" class="space-y-6">
                    <!-- Company Information -->
                    <div class="space-y-4">
                        <h2 class="text-xl font-semibold text-gray-800">Company Information</h2>

                        <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
             `               <div>
                                <label class="block text-sm font-medium text-gray-700">Company Name</label>
                                <input type="text" id="companyName" class="block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500" required>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Industry</label>
                                <select id="industry" class="block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                    <option value="">Select Industry</option>
                                    <option value="retail">Retail</option>
                                    <option value="technology">Technology</option>
                                    <option value="healthcare">Healthcare</option>
                                    <option value="finance">Finance</option>
                                    <option value="entertainment">Entertainment</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- Contact Information -->
                    <div class="space-y-4">
                        <h2 class="text-xl font-semibold text-gray-800">Contact Information</h2>

                        <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Contact Name</label>
                                <input type="text" id="contactName" class="block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500" required>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Email</label>
                                <input type="email" id="email" class="block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500" required>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Phone</label>
                                <input type="tel" id="phone" class="block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500" required>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Job Title</label>
                                <input type="text" id="jobTitle" class="block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500" required>
                            </div>
                        </div>
                    </div>

                    <!-- Campaign Details -->
                    <div class="space-y-4">
                        <h2 class="text-xl font-semibold text-gray-800">Campaign Details</h2>

                        <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Monthly Budget</label>
                                <select id="budget" class="block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                    <option value="">Select Budget Range</option>
                                    <option value="5000-10000">$5,000 - $10,000</option>
                                    <option value="10000-25000">$10,000 - $25,000</option>
                                    <option value="25000-50000">$25,000 - $50,000</option>
                                    <option value="50000+">$50,000+</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Campaign Duration</label>
                                <select id="duration" class="block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                    <option value="">Select Duration</option>
                                    <option value="3">3 months</option>
                                    <option value="6">6 months</option>
                                    <option value="12">12 months</option>
                                </select>
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">Target Audience</label>
                            <div class="grid grid-cols-2 gap-2 mt-2">
                                <label class="inline-flex items-center">
                                    <input type="checkbox" class="text-blue-600 border-gray-300 rounded shadow-sm focus:border-blue-500 focus:ring-blue-500" name="audience" value="18-24">
                                    <span class="ml-2">18-24 years</span>
                                </label>
                                <label class="inline-flex items-center">
                                    <input type="checkbox" class="text-blue-600 border-gray-300 rounded shadow-sm focus:border-blue-500 focus:ring-blue-500" name="audience" value="25-34">
                                    <span class="ml-2">25-34 years</span>
                                </label>
                                <label class="inline-flex items-center">
                                    <input type="checkbox" class="text-blue-600 border-gray-300 rounded shadow-sm focus:border-blue-500 focus:ring-blue-500" name="audience" value="35-44">
                                    <span class="ml-2">35-44 years</span>
                                </label>
                                <label class="inline-flex items-center">
                                    <input type="checkbox" class="text-blue-600 border-gray-300 rounded shadow-sm focus:border-blue-500 focus:ring-blue-500" name="audience" value="45+">
                                    <span class="ml-2">45+ years</span>
                                </label>
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">Campaign Goals</label>
                            <textarea id="goals" rows="3" class="block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500" placeholder="Describe your campaign objectives..."></textarea>
                        </div>
                    </div>

                    <div class="flex items-center justify-between pt-4">
                        <button type="button" id="fillMockData" class="inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            Fill Mock Data
                        </button>
                        <button type="submit" class="inline-flex items-center px-6 py-3 text-base font-medium text-white bg-blue-600 border border-transparent rounded-md shadow-sm hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            Submit Application
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        document.getElementById('fillMockData').addEventListener('click', () => {
            // Fill company information
            document.getElementById('companyName').value = faker.company.companyName();
            document.getElementById('industry').value = ['retail', 'technology', 'healthcare', 'finance', 'entertainment'][Math.floor(Math.random() * 5)];

            // Fill contact information
            document.getElementById('contactName').value = faker.name.findName();
            document.getElementById('email').value = faker.internet.email();
            document.getElementById('phone').value = faker.phone.phoneNumber();
            document.getElementById('jobTitle').value = faker.name.jobTitle();

            // Fill campaign details
            document.getElementById('budget').value = ['5000-10000', '10000-25000', '25000-50000', '50000+'][Math.floor(Math.random() * 4)];
            document.getElementById('duration').value = ['3', '6', '12'][Math.floor(Math.random() * 3)];

            // Randomly check audience checkboxes
            document.querySelectorAll('input[name="audience"]').forEach(checkbox => {
                checkbox.checked = Math.random() > 0.5;
            });

            // Fill campaign goals
            document.getElementById('goals').value = faker.lorem.paragraph();
        });

        document.getElementById('ctvForm').addEventListener('submit', (e) => {
            e.preventDefault();
            alert('Form submitted successfully! We will contact you soon.');
        });
    </script>
</body>
</html>

?>
