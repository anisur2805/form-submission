import { __ } from "@wordpress/i18n";
import {
	useBlockProps,
	RichText,
	AlignmentToolbar,
	BlockControls,
} from "@wordpress/block-editor";

import "./editor.scss";
import TableComponent from "./TableComponent";

export default function Edit({ attributes, className, setAttributes }) {
	const onChangeTitleAlignment = (newAlignment) => {
		setAttributes({
			titleAlignment: newAlignment === undefined ? "none" : newAlignment,
		});
	};

	const onChangeContentAlignment = (newAlignment) => {
		setAttributes({
			contentAlignment: newAlignment === undefined ? "none" : newAlignment,
		});
	};

	const onChangeTitle = (newTitle) => {
		setAttributes({
			title: newTitle,
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
				className="report-title"
				style={{ textAlign: attributes.titleAlignment }}
				value={attributes.title}
				onChange={onChangeTitle}
				placeholder={__("Report Table", "afs-fs")}
			/>
			<BlockControls>
				<AlignmentToolbar
					value={attributes.contentAlignment}
					onChange={onChangeContentAlignment}
				/>
			</BlockControls>
			<RichText
				className="report-content"
				tagName="p"
				style={{ textAlign: attributes.contentAlignment }}
				value={attributes.content}
				onChange={(newParagraph) => setAttributes({ content: newParagraph })}
				placeholder={__(
					"This list is only visible for logged-in and minimum of Editor roles.",
					"afs-fs",
				)}
			/>

			<TableComponent
				rowsPerPage={attributes.rowsPerPage}
				showPagination={attributes.showPagination}
				setAttributes={setAttributes}
			/>
		</div>
	);
}
