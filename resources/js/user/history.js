document.addEventListener('DOMContentLoaded', function() {
    const transactionRows = document.getElementById('transactionRows');
    const filterSelect = document.getElementById('filterSelect');
    const searchInput = document.getElementById('searchInput');
    const prevPageBtn = document.getElementById('prevPage');
    const nextPageBtn = document.getElementById('nextPage');
    const pageInfo = document.getElementById('pageInfo');
    const tabs = document.querySelectorAll('.Tab');
    const transactionsTable = document.getElementById('transactionsTable');
    const inquiriesTable = document.getElementById('inquiriesTable');
    const inquiryRows = document.getElementById('inquiryRows');

    let currentPage = 1;
    const itemsPerPage = 5;
    let filteredTransactions = [];
    let filteredInquiries = [];
    let currentTab = 'transactions';

    const transactions = [
        { id: 1, type: 'increase', amount: '100,000', date: 'پنج‌شنبه 10 خرداد 1403', time: '15:24' },
        { id: 2, type: 'decrease', amount: '2,500', date: 'پنج‌شنبه 10 خرداد 1403', time: '15:24' },
        { id: 3, type: 'decrease', amount: '45,000', date: 'پنج‌شنبه 10 خرداد 1403', time: '15:24' },
        { id: 4, type: 'decrease', amount: '20,000', date: 'پنج‌شنبه 10 خرداد 1403', time: '15:24' },
        { id: 5, type: 'increase', amount: '200,000', date: 'پنج‌شنبه 10 خرداد 1403', time: '15:24' },
        // Add more transactions as needed
    ];

    const inquiries = [
        { id: 1, title: 'استعلام خلافی خودرو', date: 'پنج‌شنبه 10 خرداد 1403', time: '15:24', amount: '20,000' },
        { id: 2, title: 'محاسبه شبا', date: 'پنج‌شنبه 10 خرداد 1403', time: '15:24', amount: '2,500' },
        { id: 3, title: 'استعلام وضعیت نظام وظیفه', date: 'پنج‌شنبه 10 خرداد 1403', time: '15:24', amount: '45,000' },
        // Add more inquiries as needed
    ];

    function renderTransactions() {
        console.log("hi");
        const startIndex = (currentPage - 1) * itemsPerPage;
        const endIndex = startIndex + itemsPerPage;
        const displayedTransactions = filteredTransactions.slice(startIndex, endIndex);

        transactionRows.innerHTML = displayedTransactions.map(transaction => `
            <div class="TableRow w-full flex md:self-stretch flex-col md:flex-row ${transaction.type === 'increase' ? 'bg-green-25 hover:bg-green-100' : 'bg-red-25 hover:bg-red-100'} border-t border-zinc-300 justify-between items-center p-4">
                <div class="Content w-full md:w-20 flex justify-evenly md:justify-center items-center mb-2 md:mb-0">
                    <div class="DataType md:hidden font-medium">ردیف:</div>
                    <div class="DataType text-right text-zinc-800 text-sm font-medium font-['IRANSansWebFaNum'] leading-normal">${transaction.id}</div>
                </div>
                <div class="Content w-full md:flex-1 flex justify-evenly md:justify-center items-center mb-2 md:mb-0">
                    <div class="DataType md:hidden font-medium">زمان تراکنش:</div>
                    <div class="DataType text-right md:text-center text-zinc-800 text-sm font-medium font-['IRANSansWebFaNum'] leading-normal">${transaction.date}<br/>ساعت ${transaction.time}</div>
                </div>
                <div class="Content w-full md:flex-1 flex justify-evenly md:justify-center items-center mb-2 md:mb-0">
                    <div class="DataType md:hidden font-medium">مبلغ:</div>
                    <div class="DataType text-right md:text-center text-zinc-800 text-sm font-medium font-['IRANSansWebFaNum'] leading-normal">${transaction.amount} تومان</div>
                </div>
                <div class="Content w-full md:flex-1 flex justify-evenly md:justify-center items-center mb-2 md:mb-0">
                    <div class="DataType md:hidden font-medium">نوع تراکنش:</div>
                    <div class="DataType text-right md:text-center ${transaction.type === 'increase' ? 'text-green-600' : 'text-red-600'} text-sm font-medium font-['IRANSansWebFaNum'] leading-normal">
                        ${transaction.type === 'increase' ? 'افزایش اعتبار کیف‌پول' : 'برداشت از کیف پول'}
                    </div>
                </div>
                <div class="Content w-full md:flex-1 flex justify-end md:justify-center items-center">
                    <button class="DocumentDownload w-6 h-6 flex justify-center items-center bg-sky-500 text-white rounded-full p-1 hover:bg-sky-600 transition-colors duration-300">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-download"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="7 10 12 15 17 10"></polyline><line x1="12" y1="15" x2="12" y2="3"></line></svg>
                    </button>
                </div>
            </div>
        `).join('');

        updatePagination();
    }

    function renderInquiries() {
        const startIndex = (currentPage - 1) * itemsPerPage;
        const endIndex = startIndex + itemsPerPage;
        const displayedInquiries = filteredInquiries.slice(startIndex, endIndex);

        inquiryRows.innerHTML = displayedInquiries.map(inquiry => `
            <div class="TableRow w-full flex md:self-stretch flex-col md:flex-row bg-white hover:bg-gray-50 border-t border-zinc-300 justify-between items-center p-4">
                <div class="Content w-full md:w-20 flex justify-evenly md:justify-center items-center mb-2 md:mb-0">
                    <div class="DataType md:hidden font-medium">ردیف:</div>
                    <div class="DataType text-right text-zinc-800 text-sm font-medium font-['IRANSansWebFaNum'] leading-normal">${inquiry.id}</div>
                </div>
                <div class="Content w-full md:flex-1 flex justify-evenly md:justify-center items-center mb-2 md:mb-0">
                    <div class="DataType md:hidden font-medium">عنوان:</div>
                    <div class="DataType text-right md:text-center text-zinc-800 text-sm font-medium font-['IRANSansWebFaNum'] leading-normal">${inquiry.title}</div>
                </div>
                <div class="Content w-full md:flex-1 flex justify-evenly md:justify-center items-center mb-2 md:mb-0">
                    <div class="DataType md:hidden font-medium">زمان استعلام:</div>
                    <div class="DataType text-right md:text-center text-zinc-800 text-sm font-medium font-['IRANSansWebFaNum'] leading-normal">${inquiry.date}<br/>ساعت ${inquiry.time}</div>
                </div>
                <div class="Content w-full md:flex-1 flex justify-evenly md:justify-center items-center mb-2 md:mb-0">
                    <div class="DataType md:hidden font-medium">مبلغ:</div>
                    <div class="DataType text-right md:text-center text-zinc-800 text-sm font-medium font-['IRANSansWebFaNum'] leading-normal">${inquiry.amount} تومان</div>
                </div>
                <div class="Content w-full md:flex-1 flex justify-end md:justify-center items-center">
                    <button class="DocumentDownload w-6 h-6 flex justify-center items-center bg-sky-500 text-white rounded-full p-1 hover:bg-sky-600 transition-colors duration-300 mr-2">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-download"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="7 10 12 15 17 10"></polyline><line x1="12" y1="15" x2="12" y2="3"></line></svg>
                    </button>
                    <button class="Eye w-6 h-6 flex justify-center items-center bg-sky-500 text-white rounded-full p-1 hover:bg-sky-600 transition-colors duration-300">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-eye"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg>
                    </button>
                </div>
            </div>
        `).join('');

        updatePagination();
    }

    function updatePagination() {
        const totalItems = currentTab === 'transactions' ? filteredTransactions.length : filteredInquiries.length;
        const totalPages = Math.ceil(totalItems / itemsPerPage);
        pageInfo.textContent = `${currentPage} از ${totalPages}`;
        prevPageBtn.disabled = currentPage === 1;
        nextPageBtn.disabled = currentPage === totalPages;
    }

    function filterTransactions() {
        const filterValue = filterSelect.value;
        const searchValue = searchInput.value.toLowerCase();

        filteredTransactions = transactions.filter(transaction => {
            const matchesFilter = filterValue === 'all' || transaction.type === filterValue;
            const matchesSearch = transaction.amount.toLowerCase().includes(searchValue) ||
                                  transaction.date.toLowerCase().includes(searchValue) ||
                                  transaction.time.toLowerCase().includes(searchValue);
            return matchesFilter && matchesSearch;
        });

        renderTransactions();
    }

    function filterInquiries() {
        const filterValue = filterSelect.value;
        const searchValue = searchInput.value.toLowerCase();

        filteredInquiries = inquiries.filter(inquiry => {
            const matchesFilter = filterValue === 'all' || (filterValue === 'increase' && parseFloat(inquiry.amount.replace(',', '')) > 0);
            const matchesSearch = inquiry.title.toLowerCase().includes(searchValue) ||
                                  inquiry.date.toLowerCase().includes(searchValue) ||
                                  inquiry.time.toLowerCase().includes(searchValue) ||
                                  inquiry.amount.toLowerCase().includes(searchValue);
            return matchesFilter && matchesSearch;
        });

        renderInquiries();
    }

    function switchTab(tab) {
        currentTab = tab;
        currentPage = 1; // Reset the current page when switching tabs
        if (tab === 'transactions') {
            transactionsTable.style.display = 'flex';
            inquiriesTable.style.display = 'none';
            filterTransactions();
        } else {
            transactionsTable.style.display = 'none';
            inquiriesTable.style.display = 'flex';
            filterInquiries();
        }
        updateActiveTab();
    }

    function updateActiveTab() {
        tabs.forEach(tab => {
            if (tab.dataset.tab === currentTab) {
                tab.classList.add('active');
                tab.querySelector('div:first-child').classList.add('text-sky-400');
                tab.querySelector('div:first-child').classList.remove('text-neutral-500');
                tab.querySelector('div:last-child').classList.add('border-2', 'border-sky-400');
                tab.querySelector('div:last-child').classList.remove('border', 'border-zinc-100');
            } else {
                tab.classList.remove('active');
                tab.querySelector('div:first-child').classList.remove('text-sky-400');
                tab.querySelector('div:first-child').classList.add('text-neutral-500');
                tab.querySelector('div:last-child').classList.remove('border-2', 'border-sky-400');
                tab.querySelector('div:last-child').classList.add('border', 'border-zinc-100');
            }
        });
    }

    filterSelect.addEventListener('change', () => {
        if (currentTab === 'transactions') {
            filterTransactions();
        } else {
            filterInquiries();
        }
    });

    searchInput.addEventListener('input', () => {
        if (currentTab === 'transactions') {
            filterTransactions();
        } else {
            filterInquiries();
        }
    });

    prevPageBtn.addEventListener('click', () => {
        if (currentPage > 1) {
            currentPage--;
            if (currentTab === 'transactions') {
                renderTransactions();
            } else {
                renderInquiries();
            }
        }
    });

    nextPageBtn.addEventListener('click', () => {
        const totalItems = currentTab === 'transactions' ? filteredTransactions.length : filteredInquiries.length;
        const totalPages = Math.ceil(totalItems / itemsPerPage);
        if (currentPage < totalPages) {
            currentPage++;
            if (currentTab === 'transactions') {
                renderTransactions();
            } else {
                renderInquiries();
            }
        }
    });

    tabs.forEach(tab => {
        tab.addEventListener('click', () => {
            switchTab(tab.dataset.tab);
        });
    });

    // Initial render
    filteredTransactions = transactions;
    filteredInquiries = inquiries;
    renderTransactions();
    updateActiveTab();
});