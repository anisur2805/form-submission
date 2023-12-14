;(function ($) {
	$("table.wp-list-table.reports").on(
		"click",
		"a.submit_delete",
		function (e) {
			e.preventDefault();

			if (!confirm(afsSubObj.confirm)) {
				return;
			}

			var self = $(this),
				id = self.data("delete-id");

			wp.ajax
				.send("afs-delete", {
					data: {
						security: afsSubObj.nonce,
						id: id,
					},
				})
				.done(function (response) {
					self.closest("tr")
						.css("background-color", "red")
						.hide(400, function () {
							$(this).remove();
						});
				})
				.fail(function () {
					alert(afsSubObj.error);
				});
		}
	);
})(jQuery);