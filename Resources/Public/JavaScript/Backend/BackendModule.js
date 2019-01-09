require(['jquery',
	'TYPO3/CMS/Backend/Modal',
	'TYPO3/CMS/Backend/Severity'
], function ($, Modal, Severity) {
	$('.delete-action').on('click', function (e) {
		e.preventDefault();

		let $this = $(this),
			url = $this.attr('href'),
			modal = Modal.confirm($this.data('title'), $this.data('text'), Severity.warning);

		modal.on('confirm.button.cancel', function () {
			Modal.dismiss(modal);
		});

		modal.on('confirm.button.ok', function () {
			Modal.dismiss(modal);
			window.location.href = url;
		});
	});
});