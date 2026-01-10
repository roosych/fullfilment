"use strict";
let KTUsersPermissionsList = (function () {
    let t, e;
    return {
        init: function () {
            (e = document.querySelector("#positions_table")) &&
            ((t = $(e).DataTable({
                pageLength: 25,
                info: false,
                order: [],
                columnDefs: [
                    { orderable: false, targets: 1 },
                ],
            })),
                document.querySelector('[data-kt-permissions-table-filter="search"]').addEventListener("keyup", function (e) {
                    t.search(e.target.value).draw();
                }));
        },
    };
})();
KTUtil.onDOMContentLoaded(function () {
    KTUsersPermissionsList.init();
});
