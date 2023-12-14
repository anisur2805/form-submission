import { __ } from "@wordpress/i18n";
const PaginationComponent = (props) => {
	const { current_page, total_pages, onChangePage, showPagi } = props;

	const renderPaginationLinks = () => {
		const paginationLinks = [];
		for (let i = 1; i <= total_pages; i++) {
			paginationLinks.push(
				<button
					key={i}
					onClick={() => onChangePage(i)}
					className={`page-numbers ${current_page === i ? " current " : ""}`}
				>
					{i}
				</button>,
			);
		}
		return paginationLinks;
	};

	// Render pagination links
	return (
		<div className="afs-footer-pagination">
			{showPagi && (
				<div className="pagination">
					{current_page > 1 && (
						<button className="prev-btn" onClick={() => onChangePage(current_page - 1)}>
							{__("<< Previous", "afs-form")}
						</button>
					)}
					{renderPaginationLinks()}
					{current_page < total_pages && (
						<button className="next-btn" onClick={() => onChangePage(current_page + 1)}>
							{__("Next >>", "afs-form")}
						</button>
					)}
				</div>
			)}
		</div>
	);
};

export default PaginationComponent;
