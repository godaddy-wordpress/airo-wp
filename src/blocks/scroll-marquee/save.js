import { useBlockProps } from '@wordpress/block-editor';

export default function ScrollMarqueeSave({ attributes }) {
	const {
		rows,
		scrollSpeed,
		imageHeight,
		imageWidth,
		objectFit,
		gap,
		rowGap,
	} = attributes;
	const borderRadius = attributes.style?.border?.radius;

	const blockProps = useBlockProps.save({
		className: 'airo-wp-scroll-marquee',
		'data-scroll-speed': scrollSpeed,
		style: {
			'--airo-wp-marquee-gap': gap,
			'--airo-wp-marquee-row-gap': rowGap,
			'--airo-wp-marquee-image-height': imageHeight,
			'--airo-wp-marquee-image-width': imageWidth,
			'--airo-wp-marquee-object-fit': objectFit,
		},
	});

	return (
		<div {...blockProps}>
			{rows.map((row, rowIndex) => (
				<div
					key={rowIndex}
					className="airo-wp-scroll-marquee__row"
					data-direction={row.direction}
				>
					<div className="airo-wp-scroll-marquee__track">
						{/* Render images 6 times for seamless infinite scroll */}
						{[...Array(6)].map((_, repeatIndex) => (
							<div
								key={repeatIndex}
								className="airo-wp-scroll-marquee__track-segment"
							>
								{row.images.map((image, imageIndex) => (
									<img
										key={`${repeatIndex}-${imageIndex}`}
										src={image.url}
										alt={image.alt || ''}
										className="airo-wp-scroll-marquee__image"
										loading="lazy"
										style={
											borderRadius
												? { borderRadius }
												: undefined
										}
									/>
								))}
							</div>
						))}
					</div>
				</div>
			))}
		</div>
	);
}
