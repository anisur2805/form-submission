import { __ } from "@wordpress/i18n";
import { InspectorControls } from "@wordpress/block-editor";
import PaginationComponent from "./Pagination";
import {
	ToggleControl,
	RangeControl,
	PanelBody,
	PanelRow,
} from "@wordpress/components";
const { useState, useEffect } = wp.element;
import { more } from "@wordpress/icons";

export default function TableComponent({
	rowsPerPage,
	setAttributes,
}) {
	const [showPagi, setShowPagi] = useState(true);
	const [loading, setLoading] = useState(true);
	const [reportData, setReportData] = useState({
		data: [],
		current_page: 1,
		per_page: 10,
		total_pages: 5,
	});

	const handlePageChange = (newPage) => {
		setReportData((prevState) => ({
			...prevState,
			current_page: newPage,
		}));
	};

	const handleRowsPerPage = (perPage) => {
		setAttributes({ rowsPerPage: perPage });
	};

	useEffect(() => {
		const fetchReportData = async () => {
			try {
				const response = await fetch(
					`/wp-json/afs-forms/v1/reports?per_page=${rowsPerPage}&page=${reportData.current_page}`,
					{
						headers: {
							"Content-Type": "application/json",
							"X-WP-Nonce": afsFormObj.nonce,
						},
						credentials: "same-origin",
					},
				);

				const totalPages = response.headers.get("X-WP-TotalPages");

				if (!response.ok) {
					throw new Error(`Error fetching data: ${response.statusText}`);
				}

				const data = await response.json();

				setReportData((prevState) => ({
					...prevState,
					data: data,
					total_pages: totalPages,
				}));
				setLoading(false);
			} catch (error) {
				setLoading(false);
			}
		};

		fetchReportData();
	}, [reportData.current_page, reportData.per_page, rowsPerPage]);

	return (
		<div>
			<InspectorControls>
				<PanelBody
					className="afs-report-table-panel-body"
					title={__( 'Report Table Settings', 'afs-form' ) }
					icon={more}
					initialOpen={true}
				>
					<PanelRow>
						<RangeControl
							label={__("Rows Per Page", "afs-fs")}
							value={rowsPerPage}
							min="1"
							onChange={handleRowsPerPage}
						/>
					</PanelRow>
					<PanelRow>
						<ToggleControl
							label={__("Toggle Pagination", "afs-fs")}
							checked={showPagi}
							onChange={(newValue) => {
								setShowPagi(newValue);
								setAttributes({showPagination: newValue})
							}}
						/>
					</PanelRow>
				</PanelBody>
			</InspectorControls>

			<table class="afs-report-table">
				<thead>
					<tr>
						<th>{__("ID", "afs-fs")}</th>
						<th>{__("Amount", "afs-fs")}</th>
						<th>{__("Buyer", "afs-fs")}</th>
						<th>{__("Receipt ID", "afs-fs")}</th>
						<th>{__("Buyer Email", "afs-fs")}</th>
						<th>{__("Entry Date", "afs-fs")}</th>
					</tr>
				</thead>

				<tbody>
					{loading && (
						<tr>
							<td colSpan="6">{__("Report data loading...", "afs-fs")}</td>
						</tr>
					)}

					{!loading &&
						reportData &&
						(Array.isArray(reportData.data)
							?
							  reportData.data.map((item) => (
									<tr key={item.id}>
										<td>{item.id}</td>
										<td>{item.amount}</td>
										<td>{item.buyer}</td>
										<td>{item.receipt_id}</td>
										<td>{item.buyer_email}</td>
										<td>{item.entry_at}</td>
									</tr>
							  ))
							:
							  reportData.map((item) => (
									<tr key={item.id}>
										<td>{item.id}</td>
										<td>{item.amount}</td>
										<td>{item.buyer}</td>
										<td>{item.receipt_id}</td>
										<td>{item.buyer_email}</td>
										<td>{item.entry_at}</td>
									</tr>
							  )))}

					{!loading &&
						(!reportData ||
							(Array.isArray(reportData.data)
								? reportData.data.length === 0
								: reportData.length === 0)) && (
							<tr>
								<td colSpan="6">{__("No report found", "afs-fs")}</td>
							</tr>
						)}
				</tbody>
			</table>

			<PaginationComponent
				current_page={reportData.current_page}
				total_pages={reportData.total_pages}
				onChangePage={handlePageChange}
				showPagi={showPagi}
			/>
		</div>
	);
}
