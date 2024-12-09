jQuery(document).ready(function ($) {
    let currentPage = 1;
    const booksPerPage = 12;

    function fetchBooks(page = 1) {
        const searchQuery = $('#search-input').val() || '';
        const language = $('#language-filter').val() || '';
        const author = $('#author-filter').val() || '';
        const subject = $('#subject-filter').val() || '';

        $('#books-list').html('<p>Loading books...</p>');

        $.ajax({
            url: booksCatalogue.ajax_url,
            method: 'POST',
            data: {
                action: 'fetch_books',
                page: page,
                books_per_page: booksPerPage,
                search: searchQuery,
                language: language,
                author: author,
                subject: subject,
            },
            success: function (response) {
                if (!response.success) {
                    $('#books-list').html('<p>Error loading books. Please try again.</p>');
                    return;
                }

                const { books, total_pages } = response.data;
                renderBooks(books);
                renderPagination(total_pages, page);
            },
            error: function () {
                $('#books-list').html('<p>Failed to fetch books. Please try again later.</p>');
            },
        });
    }

    function renderBooks(books) {

        if (books.length === 0) {
            $('#books-list').html('<p>No books found.</p>');
            return;
        }

        let booksHtml = '';
        books.forEach((book) => {
            console.log(book);
            const downloadLink = book.formats["application/octet-stream"] || "#"; // Fallback to "#" if no link
            const imageUrl = book.formats["image/jpeg"] || 'https://via.placeholder.com/150'; // Default placeholder if no image

            booksHtml += `
            <div class="book-card">
                <img src="${imageUrl}" alt="${book.title}" class="book-image">
                <h3>${book.title}</h3>
                <p><strong>Author:</strong> ${book.authors.map((a) => a.name).join(', ')}</p>
                <p><strong>Subjects:</strong> ${book.subjects.join(', ')}</p>
                <p><strong>Languages:</strong> ${book.languages.join(', ')}</p>
                <a href="${downloadLink}" class="download-btn" rel="noopener noreferrer">Download</a>
            </div>
        `;
        });

        $('#books-list').html(booksHtml);
    }

    function renderPagination(totalPages, currentPage) {
        let paginationHtml = '';

        if (totalPages > 1) {
            if (currentPage > 1) {
                paginationHtml += `<button class="pagination-btn" data-page="${currentPage - 1}">Previous</button>`;
            }


            if (currentPage < totalPages) {
                paginationHtml += `<button class="pagination-btn" data-page="${currentPage + 1}">Next</button>`;
            }
        }

        $('#pagination').html(paginationHtml);
    }

    $('#pagination').on('click', '.pagination-btn', function () {
        const page = parseInt($(this).data('page'));
        currentPage = page;
        fetchBooks(page);
    });

    $('#books-filter-form').on('submit', function (e) {
        e.preventDefault();
        currentPage = 1;
        fetchBooks();
    });

    fetchBooks();
});
