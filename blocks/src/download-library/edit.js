/**
 * WordPress dependencies
 */
import { __ } from '@wordpress/i18n';
import { useBlockProps, InspectorControls } from '@wordpress/block-editor';
import { PanelBody, SelectControl, RangeControl, FormTokenField } from '@wordpress/components';
import ServerSideRender from '@wordpress/server-side-render';
import { useSelect } from '@wordpress/data';

/**
 * Render the PMPro Download Library block in the editor.
 *
 * @param {Object} props Block props.
 * @return {WPElement} Element to render.
 */
export default function Edit( { attributes, setAttributes } ) {
	const blockProps = useBlockProps();
	const { template, layout, columns, label, limit, orderby, order, category } = attributes;

	// Fetch all download categories.
	const downloadCategories = useSelect( ( select ) => {
		const { getEntityRecords } = select( 'core' );
		return getEntityRecords( 'taxonomy', 'pmpro_download_category', {
			per_page: 100,
			hide_empty: false,
		} ) || [];
	}, [] );

	// The category attribute stores a comma-separated list of term slugs.
	// Map slugs to term names for display in the token field.
	const selectedSlugs = category ? category.split( ',' ).filter( ( slug ) => slug ) : [];
	const selectedCategoryNames = selectedSlugs.map( ( slug ) => {
		const term = downloadCategories.find( ( cat ) => cat.slug === slug );
		return term ? term.name : slug;
	} );

	// Update the category attribute from the token field's term names.
	const onChangeCategories = ( tokens ) => {
		const slugs = tokens
			.map( ( token ) => {
				const term = downloadCategories.find( ( cat ) => cat.name === token );
				return term ? term.slug : null;
			} )
			.filter( ( slug ) => slug );
		setAttributes( { category: slugs.join( ',' ) } );
	};

	// Template options.
	const templateOptions = [
		{ value: 'link', label: __( 'Link', 'pmpro-downloads' ) },
		{ value: 'card', label: __( 'Card', 'pmpro-downloads' ) },
		{ value: 'button', label: __( 'Button', 'pmpro-downloads' ) },
	];

	// Layout options.
	const layoutOptions = [
		{ value: 'list', label: __( 'List', 'pmpro-downloads' ) },
		{ value: 'grid', label: __( 'Grid', 'pmpro-downloads' ) },
	];

	// Label options.
	const labelOptions = [
		{ value: 'title', label: __( 'Title', 'pmpro-downloads' ) },
		{ value: 'filename', label: __( 'Filename', 'pmpro-downloads' ) },
	];

	// Order by options.
	const orderbyOptions = [
		{ value: 'title', label: __( 'Title', 'pmpro-downloads' ) },
		{ value: 'date', label: __( 'Date', 'pmpro-downloads' ) },
	];

	// Order options.
	const orderOptions = [
		{ value: 'asc', label: __( 'Ascending', 'pmpro-downloads' ) },
		{ value: 'desc', label: __( 'Descending', 'pmpro-downloads' ) },
	];

	return (
		<>
			<InspectorControls>
				<PanelBody title={ __( 'Display Settings', 'pmpro-downloads' ) }>
					<SelectControl
						label={ __( 'Template', 'pmpro-downloads' ) }
						help={ __( 'Choose how each download is displayed.', 'pmpro-downloads' ) }
						value={ template }
						options={ templateOptions }
						onChange={ ( value ) => setAttributes( { template: value } ) }
					/>
					<SelectControl
						label={ __( 'Layout', 'pmpro-downloads' ) }
						value={ layout }
						options={ layoutOptions }
						onChange={ ( value ) => setAttributes( { layout: value } ) }
					/>
					{ layout === 'grid' && (
						<RangeControl
							label={ __( 'Columns', 'pmpro-downloads' ) }
							value={ columns }
							onChange={ ( value ) => setAttributes( { columns: value } ) }
							min={ 2 }
							max={ 3 }
						/>
					) }
					<SelectControl
						label={ __( 'Label', 'pmpro-downloads' ) }
						help={ __( 'Display the download title or the filename.', 'pmpro-downloads' ) }
						value={ label }
						options={ labelOptions }
						onChange={ ( value ) => setAttributes( { label: value } ) }
					/>
				</PanelBody>
				<PanelBody title={ __( 'Query Settings', 'pmpro-downloads' ) } initialOpen={ false }>
					<FormTokenField
						label={ __( 'Categories', 'pmpro-downloads' ) }
						value={ selectedCategoryNames }
						suggestions={ downloadCategories.map( ( cat ) => cat.name ) }
						onChange={ onChangeCategories }
						__experimentalExpandOnFocus
						__experimentalShowHowTo={ false }
					/>
					<RangeControl
						label={ __( 'Limit', 'pmpro-downloads' ) }
						help={ __( 'Number of downloads to show. Use -1 for all.', 'pmpro-downloads' ) }
						value={ limit }
						onChange={ ( value ) => setAttributes( { limit: value } ) }
						min={ -1 }
						max={ 100 }
					/>
					<SelectControl
						label={ __( 'Order By', 'pmpro-downloads' ) }
						value={ orderby }
						options={ orderbyOptions }
						onChange={ ( value ) => setAttributes( { orderby: value } ) }
					/>
					<SelectControl
						label={ __( 'Order', 'pmpro-downloads' ) }
						value={ order }
						options={ orderOptions }
						onChange={ ( value ) => setAttributes( { order: value } ) }
					/>
				</PanelBody>
			</InspectorControls>
			<div { ...blockProps }>
				<ServerSideRender
					block="pmpro-downloads/download-library"
					attributes={ attributes }
				/>
			</div>
		</>
	);
}
