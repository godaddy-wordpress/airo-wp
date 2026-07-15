import { __ } from '@wordpress/i18n';
import DsgoBlockPlaceholder from '../../../components/shared/DsgoBlockPlaceholder';
import queryTemplates from '../templates';

export default function QueryPlaceholder({ clientId, setAttributes }) {
	return (
		<DsgoBlockPlaceholder
			clientId={clientId}
			setAttributes={setAttributes}
			icon="editor-table"
			label={__('Dynamic Query', 'airo-wp')}
			instructions={__(
				'Pick a starting layout for your query.',
				'airo-wp'
			)}
			templates={queryTemplates}
			variant="query"
		/>
	);
}
