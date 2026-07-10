<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Submit Move-Out Notice</title>

    @vite(['resources/css/app.css','resources/js/app.js'])
</head>

<body class="bg-slate-100">

<div class="min-h-screen flex items-center justify-center px-4">

    <div class="w-full max-w-lg bg-white rounded-2xl shadow-lg border border-slate-200">

        <!-- Header -->
        <div class="flex items-center gap-3 border-b border-slate-200 px-5 py-4">

            <a href="{{ route('tenant.payments.index') }}"
               class="text-slate-500 hover:text-slate-700 transition">

                <x-heroicon-o-arrow-left class="w-6 h-6"/>

            </a>

            <div>

                <h1 class="text-xl font-bold text-slate-800">
                    Submit Move-Out Notice
                </h1>

                <p class="text-sm text-slate-500">
                    Notify your landlord of your intended move-out.
                </p>

            </div>

        </div>


        <div class="p-5">

            @if(session('success'))

                <div class="mb-5 rounded-xl border border-green-200 bg-green-50 px-4 py-3 text-green-700">

                    {{ session('success') }}

                </div>

            @endif


            @if($errors->any())

                <div class="mb-5 rounded-xl border border-red-200 bg-red-50 px-4 py-3">

                    <ul class="list-disc ml-5 text-sm text-red-700">

                        @foreach($errors->all() as $error)

                            <li>{{ $error }}</li>

                        @endforeach

                    </ul>

                </div>

            @endif



            <form method="POST"
                  action="{{ route('tenant.moveout.store') }}">

                @csrf


                <div class="space-y-5">

                    <!-- Tenant Code -->
                    <div>

                        <label class="block text-sm font-semibold text-slate-700 mb-2">

                            Tenant Code

                        </label>

                        <input
                            type="text"
                            name="tenant_code"
                            value="{{ old('tenant_code') }}"
                            required
                            placeholder="Example: TNT-X82K9P"
                            class="w-full rounded-xl border border-slate-300 px-4 py-3 uppercase focus:border-slate-500 focus:ring-slate-500">

                    </div>



                    <!-- Move Out Option -->
                    <div>

                        <label class="block text-sm font-semibold text-slate-700 mb-2">

                            When do you plan to move out?

                        </label>

                        <select
                            name="notice_type"
                            id="notice_type"
                            required
                            class="w-full rounded-xl border border-slate-300 px-4 py-3 focus:border-slate-500 focus:ring-slate-500">

                            <option value="">
                                Select Move-Out Option
                            </option>

                            <option value="End of Month">
                                End of This Month
                            </option>

                            <option value="End of Semester">
                                End of Semester
                            </option>

                            <option value="Specific Date">
                                Specific Date
                            </option>

                            <option value="Other">
                                Other
                            </option>

                        </select>

                    </div>



                    <!-- Semester -->
                    <div id="semesterBox" class="hidden">

                        <label class="block text-sm font-semibold text-slate-700 mb-2">

                            Semester

                        </label>

                        <select
                            name="semester"
                            class="w-full rounded-xl border border-slate-300 px-4 py-3 focus:border-slate-500 focus:ring-slate-500">

                            <option value="">
                                Select Semester
                            </option>

                            <option value="Semester 1">
                                Semester 1
                            </option>

                            <option value="Semester 2">
                                Semester 2
                            </option>

                        </select>

                    </div>



                    <!-- Specific Date -->
                    <div id="dateBox" class="hidden">

                        <label class="block text-sm font-semibold text-slate-700 mb-2">

                            Expected Move-Out Date

                        </label>

                        <input
                            type="date"
                            name="specific_date"
                            class="w-full rounded-xl border border-slate-300 px-4 py-3 focus:border-slate-500 focus:ring-slate-500">

                    </div>



                    <!-- Comment -->
                    <div>

                        <label class="block text-sm font-semibold text-slate-700 mb-2">

                            Additional Comments (Optional)

                        </label>

                        <textarea
                            name="comment"
                            rows="3"
                            placeholder="Leave any message for your landlord..."
                            class="w-full rounded-xl border border-slate-300 px-4 py-3 resize-none focus:border-slate-500 focus:ring-slate-500">{{ old('comment') }}</textarea>

                    </div>



                    <p class="text-xs text-slate-500">

                        Your landlord will review this request before confirming your move-out.

                    </p>



                    <!-- Submit -->
                    <button
                        type="submit"
                        class="w-full rounded-xl bg-slate-800 hover:bg-slate-900 text-white py-3 font-semibold transition">

                        Submit Move-Out Notice

                    </button>

                </div>

            </form>

        </div>

    </div>

</div>


<script>

const noticeType = document.getElementById('notice_type');
const semesterBox = document.getElementById('semesterBox');
const dateBox = document.getElementById('dateBox');

function toggleFields()
{
    semesterBox.classList.add('hidden');
    dateBox.classList.add('hidden');

    if (noticeType.value === 'End of Semester') {
        semesterBox.classList.remove('hidden');
    }

    if (noticeType.value === 'Specific Date') {
        dateBox.classList.remove('hidden');
    }
}

noticeType.addEventListener('change', toggleFields);

toggleFields();

</script>

</body>
</html>