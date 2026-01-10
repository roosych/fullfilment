"use strict";
var KTAppEcommerceSalesListing = (function () {
    var e, t, n, r, o,
        a = (e, n, a) => {
            r = e[0] ? new Date(e[0]) : null;
            o = e[1] ? new Date(e[1]) : null;

            // Устанавливаем время для корректного сравнения
            if (r) {
                r.setHours(0, 0, 0, 0); // Начало дня
            }
            if (o) {
                o.setHours(23, 59, 59, 999); // Конец дня
            }

            // Удаляем предыдущий фильтр если он есть
            if ($.fn.dataTable.ext.search.length > 0) {
                $.fn.dataTable.ext.search.pop();
            }

            $.fn.dataTable.ext.search.push(function (settings, data, dataIndex) {
                // Если диапазон не выбран, показываем все
                if (!r && !o) {
                    return true;
                }

                // Получаем timestamp из data-order атрибута для Date Added (колонка 4)
                var row = t.row(dataIndex).node();
                var timestamp = parseInt($(row).find('td').eq(4).attr('data-order'));

                if (!timestamp) {
                    return false;
                }

                var orderDate = new Date(timestamp * 1000);

                // Проверяем, входит ли дата в диапазон
                if (r && o) {
                    // Оба значения установлены
                    return orderDate >= r && orderDate <= o;
                } else if (r) {
                    // Только начальная дата
                    return orderDate >= r;
                } else if (o) {
                    // Только конечная дата
                    return orderDate <= o;
                }

                return true;
            });

            t.draw();
        };

    return {
        init: function () {
            e = document.querySelector("#kt_ecommerce_sales_table");
            if (e) {
                t = $(e).DataTable({
                    info: false,
                    order: [[4, 'desc']], // Сортировка по Date Added по умолчанию
                    pageLength: 10,
                    columnDefs: [
                        { orderable: false, targets: 6 }, // Actions колонка
                        {
                            targets: [4, 5], // Date Added и Date Modified
                            type: 'num' // Числовая сортировка для timestamp
                        }
                    ],
                });

                t.on("draw", function () {
                    // Ваш код после отрисовки
                });

                // Flatpickr для выбора диапазона дат
                const flatpickrElement = document.querySelector("#kt_ecommerce_sales_flatpickr");
                n = $(flatpickrElement).flatpickr({
                    altInput: true,
                    altFormat: "d/m/Y",
                    dateFormat: "Y-m-d",
                    mode: "range",
                    onChange: function (selectedDates, dateStr, instance) {
                        a(selectedDates, dateStr, instance);
                    },
                });

                // Поиск
                document.querySelector('[data-kt-ecommerce-order-filter="search"]')
                    .addEventListener("keyup", function (e) {
                        t.search(e.target.value).draw();
                    });

                // Фильтр по статусу
                const statusFilter = document.querySelector('[data-kt-ecommerce-order-filter="status"]');
                $(statusFilter).on("change", (e) => {
                    let status = e.target.value;
                    if (status === "all") {
                        status = "";
                    }
                    t.column(2).search(status).draw();
                });

                // Очистка фильтра дат
                document.querySelector("#kt_ecommerce_sales_flatpickr_clear")
                    .addEventListener("click", (e) => {
                        n.clear();
                        // Очищаем все фильтры по датам
                        $.fn.dataTable.ext.search = [];
                        t.draw();
                    });
            }
        },
    };
})();

KTUtil.onDOMContentLoaded(function () {
    KTAppEcommerceSalesListing.init();
});
