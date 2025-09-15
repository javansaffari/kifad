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

});

