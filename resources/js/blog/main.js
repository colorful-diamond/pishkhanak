document.addEventListener('DOMContentLoaded', function() {
    const blogPosts = document.getElementById('blogPosts');
    const loadMoreBtn = document.getElementById('loadMoreBtn');
    const categoryFilter = document.getElementById('categoryFilter');
    const searchInput = document.getElementById('searchInput');

    let currentPage = 1;
    let category = '';
    let searchQuery = '';

    function fetchPosts() {
        // Simulating API call
        setTimeout(() => {
            const newPosts = generateMockPosts(4);
            renderPosts(newPosts);
            currentPage++;
        }, 500);
    }

    function renderPosts(posts) {
        const postsHTML = posts.map(post => `
            <div class="Card w-full md:w-56 pb-3 bg-white rounded-lg flex-col justify-start items-end gap-3 inline-flex mb-6 md:mb-0 shadow-sm hover:shadow-md transition-shadow duration-300">
                <div class="PhotoDetails self-stretch h-44 flex-col justify-start items-end gap-2 flex">
                    <div class="Photo self-stretch h-36 p-2 bg-zinc-100 rounded-lg flex-col justify-end items-start gap-2.5 flex">
                        <img src="${post.image}" alt="${post.title}" class="w-full h-full object-cover rounded-lg">
                        <div class="Chips px-2 py-0.5 bg-yellow-500 rounded-full justify-center items-center gap-2.5 inline-flex">
                            <div class="Label text-center text-white text-xs font-normal font-['IRANSansWebFaNum'] capitalize leading-none">${post.category}</div>
                        </div>
                    </div>
                    <div class="Details self-stretch h-4 px-4 justify-between items-center inline-flex">
                        <div class="Comments justify-start items-center gap-1 flex">
                            <div class="Title text-right text-neutral-500 text-xs font-normal font-['IRANSansWebFaNum'] capitalize leading-none">${post.comments}</div>
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-neutral-500" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M18 10c0 3.866-3.582 7-8 7a8.841 8.841 0 01-4.083-.98L2 17l1.338-3.123C2.493 12.767 2 11.434 2 10c0-3.866 3.582-7 8-7s8 3.134 8 7zM7 9H5v2h2V9zm8 0h-2v2h2V9zM9 9h2v2H9V9z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        <div class="PublishTime justify-end items-center gap-1 flex">
                            <div class="Title text-right text-neutral-500 text-xs font-normal font-['IRANSansWebFaNum'] capitalize leading-none">${post.publishTime}</div>
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-neutral-500" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd" />
                            </svg>
                        </div>
                    </div>
                </div>
                <div class="Content self-stretch h-20 px-4 flex-col justify-start items-end gap-3 flex">
                    <div class="Content self-stretch text-right text-zinc-800 text-sm font-medium font-['IRANSansWebFaNum'] leading-normal">${post.title}</div>
                    <a href="${post.link}" class="Button pl-1 pr-2 py-1 bg-sky-400 rounded-lg justify-center items-center gap-1 inline-flex hover:bg-sky-500 transition-colors duration-300">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-white" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M12.293 5.293a1 1 0 011.414 0l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414-1.414L14.586 11H3a1 1 0 110-2h11.586l-2.293-2.293a1 1 0 010-1.414z" clip-rule="evenodd" />
                        </svg>
                        <span class="Value text-right text-white text-xs font-medium font-['IRANSansWebFaNum'] leading-none">بیشتر بخوانید</span>
                    </a>
                </div>
            </div>
        `).join('');

        blogPosts.innerHTML += postsHTML;
    }

    function generateMockPosts(count) {
        const categories = ['خودرو و موتور', 'اقتصاد', 'فناوری', 'سلامت'];
        const posts = [];

        for (let i = 0; i < count; i++) {
            posts.push({
                title: `عنوان مقاله ${Math.floor(Math.random() * 100)}`,
                category: categories[Math.floor(Math.random() * categories.length)],
                image: `https://picsum.photos/300/200?random=${Math.random()}`,
                comments: Math.floor(Math.random() * 50),
                publishTime: `${Math.floor(Math.random() * 4) + 1} هفته پیش`,
                link: '#'
            });
        }

        return posts;
    }

    loadMoreBtn.addEventListener('click', fetchPosts);

    categoryFilter.addEventListener('change', function() {
        category = this.value;
        blogPosts.innerHTML = '';
        currentPage = 1;
        fetchPosts();
    });

    searchInput.addEventListener('input', debounce(function() {
        searchQuery = this.value;
        blogPosts.innerHTML = '';
        currentPage = 1;
        fetchPosts();
    }, 300));

    function debounce(func, wait) {
        let timeout;
        return function executedFunction(...args) {
            const later = () => {
                clearTimeout(timeout);
                func(...args);
            };
            clearTimeout(timeout);
            timeout = setTimeout(later, wait);
        };
    }

    // Initial load
    fetchPosts();
});