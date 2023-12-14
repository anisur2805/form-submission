import {
	useBlockProps,
	RichText,
	AlignmentToolbar,
	BlockControls,
} from "@wordpress/block-editor";
import { useState } from "react";
import { __ } from "@wordpress/i18n";
import {
	__experimentalNumberControl as NumberControl,
	Button,
	TextareaControl,
	TextControl,
} from "@wordpress/components";
import "./editor.scss";

export default function Edit({ attributes, className, setAttributes }) {
	const [value, setValue] = useState("");
	const { labelTypography, labelFontSize, labelColor } = attributes;

	const onChangeTitleAlignment = (newAlignment) => {
		setAttributes({
			titleAlignment: newAlignment === undefined ? "none" : newAlignment,
		});
	};

	return (
		<div {...useBlockProps()}>
			<BlockControls>
				<AlignmentToolbar
					value={attributes.titleAlignment}
					onChange={onChangeTitleAlignment}
				/>
			</BlockControls>
			<RichText
				tagName="h2"
				className="afs-form-title"
				style={{ textAlign: attributes.titleAlignment }}
				value={attributes.title}
				onChange={(newHeading) => setAttributes({ title: newHeading })}
				placeholder={__("Form title", "afs-fs")}
			/>

			<div class="afs-form-wrapper" id="afs-form-wrapper">
				<form method="POST" id="submissionForm" class="submissionForm">
					<div class="form-row">
						<div class="form-col">
							<TextControl
								type="tel"
								label={__("Enter Amount (only number)", "afs-fs")}
								isShiftStepEnabled={true}
								className="amount"
								id="amount"
								onChange={setValue}
								shiftStep={10}
								value={value}
							/>

							<p class="afs-error amount-error"></p>
						</div>

						<div class="form-col">
							<TextControl
								label={__("Receipt ID", "afs-fs")}
								id="receipt_id"
								className="receipt_id"
								value={className}
								name="receipt_id"
							/>
							<p class="afs-error receipt_id-error"></p>
						</div>

						<div class="form-col last">
							<TextControl
								label={__("Enter Buyer (max 20 chars)", "afs-fs")}
								id="buyer"
								className="buyer"
								value={className}
								name="buyer"
							/>

							<p class="afs-error buyer-error"></p>
						</div>
					</div>

					<div class="form-row">
						<div class="form-col">
							<TextControl
								label={__("Buyer Email", "afs-fs")}
								type="email"
								id="buyer_email"
								className="buyer_email"
								value={className}
								name="buyer_email"
							/>

							<p class="afs-error buyer_email-error"></p>
						</div>
						<div class="form-col last">
							<TextControl
								type="tel"
								label={__("Entry By (only number)", "afs-fs")}
								isShiftStepEnabled={true}
								className="entry_by"
								id="entry_by"
								name="entry_by"
								onChange={setValue}
								shiftStep={10}
								value={value}
							/>

							<p class="afs-error  entry_by-error"></p>
						</div>
					</div>

					<div class="form-row">
						<div class="form-col">
							<TextControl
								label={__("City", "afs-fs")}
								type="text"
								id="city"
								className="city"
								value={className}
								name="city"
							/>

							<p class="afs-error city-error"></p>
						</div>
						<div className="form-col last">
							<TextControl
								type="tel"
								label={__("Phone", "afs-form")}
								id="phone"
								className="phone"
								value={attributes.phone}
							/>
							<p className="afs-error phone-error"></p>
						</div>
					</div>

					<div className="form-row">
						<div className="form-col last">
							<label
								htmlFor="items"
								style={{
									fontFamily: labelTypography,
									fontSize: labelFontSize,
									color: labelColor,
								}}
							>
								{__("Items (you may add multiple)", "afs-form")}
							</label>
							<div id="itemsContainer" className="itemsContainer">
								<TextControl
									type="text"
									id="items"
									className="items"
									value={attributes.items}
									onChange={(value) => setAttributes({ items: value })}
								/>
								<Button type="button" id="addItem" className="addItem">
									{__("Add Item", "afs-form")}
								</Button>
								<div className="itemsContainer_inner"></div>
								<p className="afs-error items-error"></p>
							</div>
						</div>
					</div>

					<div className="form-row">
						<div className="form-col">
							<label htmlFor="note">
								{__("Note (max 30 words)", "afs-form")}
							</label>
							<TextareaControl
								id="note"
								className="note"
								rows="4"
								value={attributes.note}
								onChange={(value) => setAttributes({ note: value })}
							/>
							<p className="afs-error note-error"></p>
						</div>
					</div>
					<div>
						<Button className="afs-gb-submit-btn" type="submit">
							{__("Submit", "afs-form")}
						</Button>
					</div>
				</form>
			</div>
		</div>
	);
}
