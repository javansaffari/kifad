document.addEventListener('DOMContentLoaded', function() {
    // Sidebar 


    const toggleButton = document.getElementById('toggleSidebarMobile');
    const sidebar = document.getElementById('sidebar');
    const backdrop = document.getElementById('sidebarBackdrop');
    const hamburgerIcon = document.getElementById('toggleSidebarMobileHamburger');
    const closeIcon = document.getElementById('toggleSidebarMobileClose');

    toggleButton.addEventListener('click', function() {
        sidebar.classList.toggle('hidden');
        backdrop.classList.toggle('hidden');
        hamburgerIcon.classList.toggle('hidden');
        closeIcon.classList.toggle('hidden');
    });

    backdrop.addEventListener('click', function() {
        sidebar.classList.add('hidden');
        backdrop.classList.add('hidden');
        hamburgerIcon.classList.remove('hidden');
        closeIcon.classList.add('hidden');
    });

      

                // سه‌رقمی کردن مبلغ
                $('.amount').on('input', function() {
                    let $this = $(this);
                    let value = $this.val().replace(/,/g, ''); // Remove commas
                
                    // Check if numeric
                    if (/^\d*$/.test(value)) {
                        $this.val(value === '' ? '' : Number(value).toLocaleString('en-US'));
                        $this.siblings('.amountError').text(''); // Clear error message
                    } else {
                        // Show error and remove last invalid character
                        $this.siblings('.amountError').text('ورودی باید فقط عدد باشد.');
                        $this.val($this.val().slice(0, -1));
                    }
                });

            // Select2 با قابلیت ایجاد تگ

            $(".select").select2({
                dir: "rtl",
                tags: true,
                tokenSeparators: [',', ' ']
            })

            $(".expensSelect, .incomSelect").select2({
                dir: "rtl",
                tags: true,
                tokenSeparators: [',', ' ']
            });

      // Initialize Persian datepickers for all relevant inputs
$("#expenseDatapicker, #incomeDatapicker, #transferDatapicker, #datapicker, #startDate, #dueDate, #issueDate, #fromDate, #toDate").persianDatepicker({
    selectedBefore: true
});


                
});



document.addEventListener('DOMContentLoaded', function() {
    const amountInput = document.getElementById('amount');

    // Function to convert Persian/Arabic digits to English
    function persianToEnglishDigits(str) {
        const persianDigits = ['۰','۱','۲','۳','۴','۵','۶','۷','۸','۹'];
        const arabicDigits  = ['٠','١','٢','٣','٤','٥','٦','٧','٨','٩'];
        let result = str;
        persianDigits.forEach((d,i) => { result = result.replaceAll(d, i); });
        arabicDigits.forEach((d,i) => { result = result.replaceAll(d, i); });
        return result;
    }

    // Function to format number with commas
    function formatNumberWithCommas(value) {
        let num = parseInt(value, 10);
        if (isNaN(num)) return '';
        return num.toLocaleString('en-US'); // comma as thousand separator
    }

    // Format on input
    amountInput.addEventListener('input', function(e) {
        let cursorPos = this.selectionStart; // preserve cursor
        let raw = persianToEnglishDigits(this.value);
        raw = raw.replace(/[^0-9]/g, ''); // remove non-digits
        this.value = formatNumberWithCommas(raw);
        // set cursor at the end
        this.selectionStart = this.selectionEnd = this.value.length;
    });

    // Before submit, convert to plain number
    const form = amountInput.closest('form');
    if (form) {
        form.addEventListener('submit', function(e) {
            let raw = persianToEnglishDigits(amountInput.value);
            raw = raw.replace(/,/g, ''); // remove commas
            amountInput.value = raw; // assign clean value for DB
        });
    }
});

