(function ($) {
	jQuery(document).ready(function ($) {
		// Handle phone number validation.
		$(".phone").on("input paste", function () {
			const $phoneInput = $(this);
			let phoneNumber = $phoneInput.val().replace(/[^0-9]|0(?=0*880)/g, "");

			if (!phoneNumber.startsWith("880")) {
				phoneNumber = "880" + phoneNumber;
			}

			phoneNumber = phoneNumber.replace(/8800+/g, "880");
			phoneNumber = phoneNumber.slice(0, 13);
			phoneNumber = phoneNumber.replace(/(\d{3})(\d{4})(\d{4})/, "$1 $2 $3");

			$phoneInput.val(phoneNumber);
		});

		// Handle amount validation.
		$(".amount").on("input paste", function () {
			let amountValue = $(this).val();
			if (isNaN(amountValue) || amountValue <= 0) {
				$(".amount-error").text(afsFormObj.number_msg);
				$(".amount").val("");
			} else {
				$(".amount-error").text("");
			}
		});

		// Handle entry by validation.
		$(".entry_by").on("input paste", function () {
			let entryByValue = $(this).val();
			if (isNaN(entryByValue) || entryByValue <= 0) {
				$(".entry_by-error").text(afsFormObj.number_msg);
				$(".entry_by").val("");
			} else {
				$(".entry_by-error").text("");
			}
		});

		// Handle buyer content 20 characters
		$(".buyer").on("input paste", function () {
			let buyerValue = $(this).val();
			let maxLengthChars = 20;
			if (buyerValue.length > maxLengthChars) {
				$(this).val(buyerValue.substring(0, maxLengthChars));
				$(".buyer-error").text(afsFormObj.length_exceed);
			} else {
				$(".buyer-error").text("");
			}
		});

		// Handle note content 30 words
		$(".note").on("input paste", function () {
			let noteValue = $(this).val();

			let wordCount = noteValue.split(/\s+/).length;
			let maxLengthWords = 30;
			if (wordCount > maxLengthWords) {
				$(".note-error").text(afsFormObj.length_exceed);
				let trimmedValue = noteValue.split(/\s+/, maxLengthWords).join(" ");
				$(this).val(trimmedValue);
			} else {
				$(".note-error").text("");
			}
		});

		// Handle receipt_id only text.
		$(".receipt_id").on("input paste", function () {
			let receiptIdValue = $(this).val();

			if (/[^a-zA-Z]/.test(receiptIdValue)) {
				$(this).val("");
				$(".receipt_id-error").text(afsFormObj.receipt_msg);
			} else {
				$(".receipt_id-error").text("");
			}
		});

		// Handle city only text and space.
		$(".city").on("input paste", function () {
			let cityValue = $(this).val();
			if (/^[a-zA-Z\s]*$/.test(cityValue)) {
				$(".city-error").text("");
			} else {
				$(this).val("");
				$(".city-error").text(afsFormObj.city_msg);
			}
		});

		// Handle buyer email.
		$(".buyer_email").on("input paste", function () {
			let emailValue = $(this).val();

			let emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
			if (emailRegex.test(emailValue)) {
				$(".buyer_email-error").text("");
			} else {
				$(".buyer_email-error").text(afsFormObj.invalid_email);
			}
		});

		// Handle form submission
		$(".submissionForm").on("submit", function (e) {
			$(".afs-error").text("");
			e.preventDefault();

			let $that = $(this),
				noticeWrapper = $(".afs-success-message"),
				formWrapper = $(this).closest(".afs-form-wrapper"),
				appendedItems = [];

			$(this)
				.find(".itemsContainer_inner .text")
				.each(function () {
					appendedItems.push($(this).text().trim());
				});
			let itemsString = appendedItems.join(", ");
			$(this).find("#items").val(itemsString);
			let formData = $(this).serializeArray();

			formData.push({ name: "action", value: "handle_submission" });

			if (getCookie("form_cookie")) {
				displayResult(formWrapper, "error", afsFormObj.resubmit_message);
			} else {
				$.ajax({
					url: afsFormObj.ajaxUrl,
					type: "POST",
					dataType: "json",
					data: formData,
					beforeSend: function () {
						$that.addClass("form-loader");
					},
					success: function (response) {
						if (response.success) {
							$that.removeClass("form-loader");
							if (false == response.show_notice) {
								$that.trigger("reset");
								$that.hide();
								displayResult($that, "success", afsFormObj.success);
							}

							if (undefined !== response.show_notice) {
								noticeWrapper.removeClass("hidden");
							}

							if ( !response.is_admin) {
								setCookie("form_cookie", "afs", 1);
							}
						} else {
							$that.removeClass("form-loader");
							let validationRules = response.errors;

							$.each(validationRules, function (field, errorMessage) {
								let value = $that.find("#" + field).val();
								if (!value) {
									$that.find("." + field + "-error").text(errorMessage);
									formValid = false;
								}
							});
						}
					},
					error: function (error) {
						$that.removeClass("form-loader");
						displayResult($that, "error", afsFormObj.error);
						displayResult($that, "error", error.msg);
					},
				});
			}
		});

		// Function to display success/error messages
		function displayResult(form, type, message) {
			let resultContainer = form
				.closest(".afs-form-wrapper")
				.find("#resultContainer");

			resultContainer.empty();
			let className = type === "success" ? "successMsg" : "errorMsg";
			resultContainer.append(
				'<p class="' + className + '">' + message + "</p>",
			);
		}

		// Function to set a cookie
		function setCookie(name, value, days) {
			let date = new Date();
			date.setTime(date.getTime() + days * 24 * 60 * 60 * 1000);
			let expires = "expires=" + date.toUTCString();
			document.cookie = name + "=" + value + ";" + expires + ";path=/";
		}

		// Function to get a cookie
		function getCookie(name) {
			let cookieName = name + "=";
			let cookies = document.cookie.split(";");
			for (let i = 0; i < cookies.length; i++) {
				let cookie = cookies[i].trim();
				if (cookie.indexOf(cookieName) === 0) {
					return cookie.substring(cookieName.length, cookie.length);
				}
			}
			return "";
		}

		// Handle multiple form in a page issue.
		$(".afs-form-wrapper form").on("click", function () {
			add_items_data(this);
		});

		function add_items_data(form) {
			let $form = $(form);

			let $itemsContainer = $form.find("#itemsContainer");
			let $itemsInput = $itemsContainer.find(".items");
			let $addItemBtn = $form.find("#addItem");
			let $itemsContainerInner = $form.find(".itemsContainer_inner");

			$itemsContainer.on("input", ".items", function () {
				let inputValue = $(this).val().trim();
				$addItemBtn.prop("disabled", inputValue === "");
			});

			$addItemBtn.on("click", function (e) {
				let inputValue = $itemsInput.val().trim();
				$(this).closest("#itemsContainer").find(".items").focus();
				if (inputValue !== "") {
					$itemsContainerInner.append(
						`<span class="item"><span class="text">${inputValue}</span><span class="item-remove">X</span></span>`,
					);
					$itemsInput.val("");
					// $addItemBtn.prop("disabled", true);
				}
			});
		}

		$("body").on("click", ".item-remove", function (e) {
			$(this).closest(".item").remove();
		});
	});
})(jQuery);
