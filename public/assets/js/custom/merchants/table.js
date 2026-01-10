$(function() {
    let MerchantsTable = (function() {
        let table, datatable;

        function init() {
            table = $("#merchants_table");
            if (table.length) {
                table.find("tbody tr").each(function() {
                    let row = $(this);
                    let dateCell = row.find("td:first");
                });

                datatable = table.DataTable({
                    info: false,
                    order: [],
                    pageLength: 10
                });

                initSearch();
            }
        }

        function initSearch() {
            $('[merchant-filter="search"]').on("keyup", function() {
                datatable.search($(this).val()).draw();
            });
        }

        return {
            init: init
        };
    })();

    MerchantsTable.init();
});
