/**
 * Ripple
 */
class Ripple {
    constructor() {
        this.openSearch = $('.open-search');
        this.superSearch = $('.super-search');
        this.searchInput = $('.search-input');
        this.closeSearch = $('.close-search');
        this.searchResult = $('.search-result');
        this.timeoutID = null;
    }

    searchFunction(keyword) {
        clearTimeout(this.timeoutID);
        this.timeoutID = setTimeout(() => {
            this.superSearch.removeClass('search-finished');
            this.searchResult.fadeOut();
            $.ajax({
                type: 'GET',
                cache: false,
                url: this.superSearch.data('search-url'),
                data: {
                    q: keyword,
                },
                success: (res) => {
                    if (!res.error) {
                        this.searchResult.html(res.data.items);
                        this.superSearch.addClass('search-finished');
                    } else {
                        this.searchResult.html(res.message);
                    }
                    this.searchResult.fadeIn(500);
                },
                error: (res) => {
                    this.searchResult.html(res.responseText);
                    this.searchResult.fadeIn(500);
                },
            });
        }, 500);
    }

    bindActionToElement() {
        this.openSearch.on('click', () => {
            event.preventDefault();
            if (this.openSearch.hasClass('active')) {
                this.openSearch.removeClass('active');
                this.superSearch.removeClass('active');
                this.superSearch.addClass('hide');
                $('body').removeClass('overflow');
                this.searchResult.hide();
                $('.quick-search > .form-control').focus();
            } else {
                $('body').addClass('overflow');
                this.openSearch.addClass('active');
                this.superSearch.removeClass('hide');
                this.superSearch.addClass('active');
                if (this.searchInput.val() !== '') {
                    this.searchFunction(this.searchInput.val());
                }
            }
        });

        this.closeSearch.on('click', (event) => {
            event.preventDefault();
            this.openSearch.removeClass('active');
            this.superSearch.removeClass('active');
            this.superSearch.addClass('hide');
            $('body').removeClass('overflow');
            this.searchResult.hide();
            $('.quick-search > .form-control').focus();
        });

        this.searchInput.keyup((e) => {
            this.searchInput.val(e.target.value);
            this.searchFunction(e.target.value);
        });
    }
}

$(() => {
    new Ripple().bindActionToElement();
});
