/**
 * WordPress dependencies...
 */

import classnames from 'classnames';

const { __ } = wp.i18n;

const {
	registerBlockType
} = wp.blocks;

const {
	BlockControls,
} = wp.editor;

const {
	Toolbar,
	Button,
	Tooltip,
} = wp.components;

/**
 * Internal dependencies
 */
import './style.scss';
import './editor.scss';
import social_list from './social_list';
import SocialIcons from './icons';

registerBlockType( 'orbitfox/sharing-icons', {
	title: __( 'Sharing Icons' ),
	description: __( 'Share buttons for your website visitors to share content on any social sharing service.' ),
	icon: 'networking',
	category: 'orbitfox',
	keywords: [
		'social media',
		'sharing',
		'icons'
	],
	attributes: {
		facebook: {
			type: 'boolean',
			default: true,
		},
		twitter: {
			type: 'boolean',
			default: true,
		},
		googleplus: {
			type: 'boolean',
			default: true,
		},
		linkedin: {
			type: 'boolean',
			default: true,
		},
		pinterest: {
			type: 'boolean',
			default: false,
		},
		tumblr: {
			type: 'boolean',
			default: false,
		},
		reddit: {
			type: 'boolean',
			default: false,
		},
	},

	supports: {
		html: true,
		align: [ 'left', 'center', 'right' ],
	},

	styles: [
		{ name: 'default', label: __( 'Regular' ), isDefault: true },
		{ name: 'icons', label: __( 'Icons Only' ) },
	],

	edit: props => {
		const toggleIcons = ( item ) => {
			props.setAttributes( { [ item ]: !props.attributes[item] } );
		};

		return [
			<BlockControls key="toolbar-controls">
				<Toolbar
					className='components-toolbar'
				>
					{ Object.keys( social_list ).map( ( item, i ) => {
						let prop = props.attributes[item];
						return (
							<Tooltip text={ __( `Display ${ social_list[item]['label'] }` )	}>
								<Button
									className={ classnames(
										'components-icon-button',
										'components-toolbar__control',
										{ 'is-active': prop },
									) }
									onClick={ ( e ) => toggleIcons( item ) }
								>
									<SocialIcons icon={ item }/>
								</Button>
							</Tooltip>
						)
					} ) }
				</Toolbar>
			</BlockControls>,

			<div
				className={ classnames(
					props.className,
					{ 'has-label': ( props.attributes.className ? ! props.attributes.className.includes( 'is-style-icons' ) : true ) },
				) }
			>
				{ Object.keys( social_list ).map( ( item, i ) => {
					if ( props.attributes[item] === true ) {
						return (
							<a className={ `social-icon is-${item}` }><i class={ `fab fa-${social_list[item]['icon']}` }></i>{ ( props.attributes.className ? ! props.attributes.className.includes( 'is-style-icons' ) : true ) && social_list[item]['label'] }</a>
						)
					}
				} ) }
			</div>
		];
	},

	save: () => {
		return null;
	},
});