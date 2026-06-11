(function ($) {

    $(document).ready(function () {
		
		let pageIsAlive = true;

		window.addEventListener('beforeunload', () => {
			pageIsAlive = false;
		});


        /* 1. AJAX table reload on filter change */

        const $filters = $('#lwd-filters');

        function reloadTable() {
            const url = $filters.attr('action');
            const data = $filters.serialize();

            $.get(url, data, function (html) {
				if (!pageIsAlive) return;

				const $newTable = $(html).find('#lwd-logs-table');
				if ($newTable.length) {
					$('#lwd-logs-table').replaceWith($newTable);
				}
			});
        }

        if ($filters.length) {
            let timer = null;
            $filters.on('input', 'input[type="search"]', function () {
                clearTimeout(timer);
                timer = setTimeout(reloadTable, 250);
            });

            $filters.on('change', 'select', function () {
                reloadTable();
            });
        }


        /* 2. Auto-save settings (AJAX) */

        const $settingsForm = $('#lwd-settings-form');

        if ($settingsForm.length) {

            const ajaxUrl = window.ajaxurl || (window.wp && wp.ajax && wp.ajax.settings && wp.ajax.settings.url);

            function saveSettings(callback) {
                const data = $settingsForm.serializeArray();

                data.push({ name: 'action', value: 'lwd_save_settings' });

				$.post(ajaxUrl, data, function () {
					if (!pageIsAlive) return;

					if (typeof callback === 'function') {
						callback();
					}
				});
            }

            // capability + log retention
            $settingsForm.on('change', '#lwd_capability, select[name="lwd_log_retention"]', function () {
                saveSettings(showToast);
            });

            // dark mode → autosave + reload
            $settingsForm.on('change', '#lwd_dark_mode', function () {
                saveSettings(function () {
                    location.reload();
                });
            });
        }


        /* 3. Table sorting */

        $(document).on('click', '#lwd-logs-table th', function () {
            const $table = $('#lwd-logs-table');
            const $tbody = $table.find('tbody');
            const index = $(this).index();
            const rows = $tbody.find('tr').get();

            const asc = !$(this).hasClass('sorted-asc');
            $('#lwd-logs-table th').removeClass('sorted-asc sorted-desc sorted-column');
            $(this).addClass(asc ? 'sorted-asc' : 'sorted-desc').addClass('sorted-column');

            rows.sort(function (a, b) {
                const A = $(a).children('td').eq(index).text().toUpperCase();
                const B = $(b).children('td').eq(index).text().toUpperCase();
                return asc ? A.localeCompare(B) : B.localeCompare(A);
            });

            $.each(rows, function (i, row) {
                $tbody.append(row);
            });
        });


        /* 4. Default sort by Date column */

        const $dateHeader = $('#lwd-logs-table th.column-date');
        if ($dateHeader.length) {
            $dateHeader.addClass('sorted-desc sorted-column');

            const $tbody = $('#lwd-logs-table tbody');
            const rows = $tbody.find('tr').get();

            rows.sort(function (a, b) {
                const A = $(a).children('td').eq($dateHeader.index()).text().toUpperCase();
                const B = $(b).children('td').eq($dateHeader.index()).text().toUpperCase();
                return B.localeCompare(A);
            });

            $.each(rows, function (i, row) {
                $tbody.append(row);
            });
        }
		
        /* 5. Notice fo autosave */
		function showToast() {
			const $toast = $('#lwd-toast');
			$toast.addClass('show').show();

			setTimeout(() => {
				$toast.removeClass('show');
				setTimeout(() => $toast.hide(), 250);
			}, 1800);
		}

    });

})(jQuery);
