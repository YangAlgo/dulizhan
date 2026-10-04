<?php
/**
 * English and Chinese starter content created by the setup wizard.
 *
 * Everything here is a starting point: edit or delete it in WordPress afterwards.
 *
 * @package Bufan
 */

defined( 'ABSPATH' ) || exit;

/**
 * Block markup helpers, so the content opens cleanly in the block editor.
 */
function bufan_block_p( $text ) {
	return "<!-- wp:paragraph -->\n<p>" . $text . "</p>\n<!-- /wp:paragraph -->\n\n";
}

/**
 * Heading block.
 *
 * @param string $text  Heading text.
 * @param int    $level Heading level.
 */
function bufan_block_h( $text, $level = 2 ) {
	$attrs = 2 === $level ? '' : ' {"level":' . (int) $level . '}';
	return '<!-- wp:heading' . $attrs . " -->\n<h" . $level . ' class="wp-block-heading">' . $text . '</h' . $level . ">\n<!-- /wp:heading -->\n\n";
}

/**
 * List block.
 *
 * @param string[] $items   List items.
 * @param bool     $ordered Numbered list.
 */
function bufan_block_list( $items, $ordered = false ) {
	$tag   = $ordered ? 'ol' : 'ul';
	$attrs = $ordered ? ' {"ordered":true}' : '';
	$html  = '<!-- wp:list' . $attrs . " -->\n<" . $tag . ' class="wp-block-list">';
	foreach ( $items as $item ) {
		$html .= "<!-- wp:list-item -->\n<li>" . $item . "</li>\n<!-- /wp:list-item -->";
	}
	return $html . '</' . $tag . ">\n<!-- /wp:list -->\n\n";
}

/**
 * Details (accordion) block for FAQs.
 *
 * @param string $question Summary text.
 * @param string $answer   Answer paragraph.
 */
function bufan_block_details( $question, $answer ) {
	return "<!-- wp:details -->\n<details class=\"wp-block-details\"><summary>" . $question . "</summary><!-- wp:paragraph -->\n<p>" . $answer . "</p>\n<!-- /wp:paragraph --></details>\n<!-- /wp:details -->\n\n";
}

/**
 * Pages: key => array( 'en' => [...], 'zh' => [...], 'template' => '' ).
 *
 * @return array
 */
function bufan_starter_pages() {
	$site = get_bloginfo( 'name' );

	return array(
		'home'          => array(
			'classic' => true,
			'en'      => array(
				'title'   => 'Home',
				'slug'    => 'home',
				'content' => bufan_block_p( 'Bufan is a family-run factory dedicated to one thing: embroidered canvas bags. From cutting the canvas to the last stitch, every step happens under our own roof — so we control the quality, keep prices fair and deliver on time.' )
					. bufan_block_p( 'Whether you run a brand, a gift shop or an event, we turn your artwork into pouches, purses and totes that your customers will love to use.' ),
			),
			'zh'      => array(
				'title'   => '首页',
				'slug'    => 'shouye',
				'content' => bufan_block_p( 'Bufan 是一家家族经营的工厂，只专注做一件事：帆布刺绣包。从裁布到最后一针，每道工序都在自己的工厂完成，所以我们能把控品质、给出实在的价格，并按时交货。' )
					. bufan_block_p( '无论您是品牌方、礼品店还是活动主办方，我们都能把您的图案做成顾客爱用的化妆包、零钱包和托特包。' ),
			),
		),
		'customization' => array(
			'template' => 'page-templates/customization.php',
			'en'       => array(
				'title'   => 'Customization',
				'slug'    => 'customization',
				'content' => bufan_block_p( 'Every Bufan bag can be made to your specification. Send us your artwork or an idea, and we will suggest the right canvas, embroidery technique and finishing for your budget.' )
					. bufan_block_h( 'What you can customize' )
					. bufan_block_list(
						array(
							'<strong>Size and shape</strong> — use our patterns or send your own drawing',
							'<strong>Canvas weight and color</strong> — light or heavy canvas, natural, dyed or Pantone matched',
							'<strong>Embroidery</strong> — your logo, illustration or text, in the thread colors you choose',
							'<strong>Hardware</strong> — zipper, puller, lining and inner pockets',
							'<strong>Branding</strong> — woven labels, hang tags and care labels',
							'<strong>Packaging</strong> — OPP bags, gift boxes, barcodes and retail-ready packing',
						)
					)
					. bufan_block_h( 'Embroidery techniques' )
					. bufan_block_h( 'Flat embroidery', 3 )
					. bufan_block_p( 'Crisp, durable and ideal for logos, text and detailed illustrations.' )
					. bufan_block_h( '3D puff embroidery', 3 )
					. bufan_block_p( 'Stitched over foam for a raised, tactile effect — great for bold logos and lettering.' )
					. bufan_block_h( 'Chenille embroidery', 3 )
					. bufan_block_p( 'A soft, looped texture that gives a cosy, retro look.' )
					. bufan_block_h( 'Appliqué', 3 )
					. bufan_block_p( 'Fabric shapes stitched onto the canvas for large, colorful designs with fewer stitches.' )
					. bufan_block_p( 'Not sure which technique suits your design? Send it to us and we will recommend one — and prove it with a sample.' ),
			),
			'zh'       => array(
				'title'   => '定制服务',
				'slug'    => 'dingzhi',
				'content' => bufan_block_p( '每一款 Bufan 包都可以按您的要求定制。把您的图案或想法发给我们，我们会根据预算推荐合适的帆布、刺绣工艺和配件。' )
					. bufan_block_h( '可定制内容' )
					. bufan_block_list(
						array(
							'<strong>尺寸和款式</strong>——可用我们现有的版型，也可以按您的图纸开发',
							'<strong>帆布克重和颜色</strong>——薄款或厚款帆布，本色、染色或按潘通色号配色',
							'<strong>刺绣</strong>——您的 logo、插画或文字，线色由您指定',
							'<strong>辅料</strong>——拉链、拉头、内衬和内袋',
							'<strong>品牌标识</strong>——织唛、吊牌和洗水标',
							'<strong>包装</strong>——OPP 袋、礼盒、条形码，可直接上架销售',
						)
					)
					. bufan_block_h( '刺绣工艺' )
					. bufan_block_h( '平绣', 3 )
					. bufan_block_p( '线迹清晰、耐用，适合 logo、文字和精细图案。' )
					. bufan_block_h( '立体绣', 3 )
					. bufan_block_p( '在海绵上刺绣，形成凸起的立体效果，适合醒目的 logo 和字母。' )
					. bufan_block_h( '毛巾绣', 3 )
					. bufan_block_p( '柔软的毛圈质感，带来温暖的复古效果。' )
					. bufan_block_h( '贴布绣', 3 )
					. bufan_block_p( '把布片缝到帆布上，用更少的针数做出大面积、色彩丰富的图案。' )
					. bufan_block_p( '不确定哪种工艺适合您的图案？发给我们，我们会推荐合适的工艺，并打样确认效果。' ),
			),
		),
		'about'         => array(
			'en' => array(
				'title'   => 'About us',
				'slug'    => 'about',
				'content' => bufan_block_p( 'Bufan is a family-run embroidery factory making canvas bags for brands and retailers around the world.' )
					. bufan_block_h( 'Our story' )
					. bufan_block_p( 'Bufan is run by a family that has spent years around embroidery machines and sewing tables. We know canvas, thread and stitches inside out, and we put that knowledge into every bag we make for our customers.' )
					. bufan_block_h( 'Our factory' )
					. bufan_block_p( 'Pattern making, cutting, embroidery, sewing, inspection and packing all happen in-house. Keeping every step under one roof lets us move quickly from sample to bulk order and keep a close eye on every bag.' )
					. bufan_block_h( 'Quality control' )
					. bufan_block_list(
						array(
							'Fabric and accessories checked on arrival',
							'Embroidery compared with the approved sample',
							'Every finished bag inspected before packing',
							'Photos or video of your goods before shipment',
						)
					)
					. bufan_block_h( 'Visit us' )
					. bufan_block_p( 'You are welcome to visit our factory, or to join a video call and see your order in production.' ),
			),
			'zh' => array(
				'title'   => '关于我们',
				'slug'    => 'guanyu',
				'content' => bufan_block_p( 'Bufan 是一家家族经营的刺绣工厂，为全球的品牌和零售商生产帆布包。' )
					. bufan_block_h( '我们的故事' )
					. bufan_block_p( 'Bufan 由一个与刺绣机和缝纫台打了多年交道的家庭经营。我们熟悉帆布、绣线和每一种针法，并把这些经验用在为客户做的每一个包上。' )
					. bufan_block_h( '我们的工厂' )
					. bufan_block_p( '打版、裁剪、刺绣、缝制、质检和包装全部在自己的工厂完成。所有工序都在一个屋檐下，让我们能更快地从打样走到大货，也能盯紧每一个包的品质。' )
					. bufan_block_h( '品质管控' )
					. bufan_block_list(
						array(
							'面料和辅料到厂检验',
							'刺绣效果对照确认样检查',
							'每个成品包装前逐一检验',
							'出货前提供货物照片或视频',
						)
					)
					. bufan_block_h( '欢迎参观' )
					. bufan_block_p( '欢迎来工厂参观，也可以通过视频通话实时查看您订单的生产情况。' ),
			),
		),
		'faq'           => array(
			'en' => array(
				'title'   => 'FAQ',
				'slug'    => 'faq',
				'content' => bufan_block_details( 'What is your minimum order quantity (MOQ)?', 'MOQ depends on the style, fabric and embroidery. Send us your design and target quantity, and we will confirm the MOQ for your order.' )
					. bufan_block_details( 'Can I get a sample before ordering?', 'Yes. We make a sample of your design before bulk production so you can check the size, colors and embroidery. Sample cost and time are included in our quotation.' )
					. bufan_block_details( 'How long does production take?', 'It depends on the design and quantity. We confirm the schedule in our quotation and keep you updated until your goods are shipped.' )
					. bufan_block_details( 'Can you embroider my own logo or artwork?', 'Yes. Send us your artwork as AI, PDF, PNG or JPG. We convert it into an embroidery file and confirm the design with you before sampling.' )
					. bufan_block_details( 'What payment methods do you accept?', 'Payment terms are confirmed in your quotation. Common options are bank transfer (T/T) and PayPal.' )
					. bufan_block_details( 'How do you ship?', 'We ship worldwide by express courier, air freight or sea freight, depending on your timeline and budget. We can also deliver to your forwarder in China.' ),
			),
			'zh' => array(
				'title'   => '常见问题',
				'slug'    => 'changjian-wenti',
				'content' => bufan_block_details( '你们的起订量（MOQ）是多少？', '起订量取决于款式、面料和刺绣。把您的图案和目标数量发给我们，我们会确认这个订单的起订量。' )
					. bufan_block_details( '下单前可以先打样吗？', '可以。大货生产前我们会先按您的设计打样，方便您确认尺寸、颜色和刺绣效果。打样费用和时间会写在报价里。' )
					. bufan_block_details( '生产需要多长时间？', '取决于款式和数量。我们会在报价中确认交期，并在发货前持续同步生产进度。' )
					. bufan_block_details( '可以绣我自己的 logo 或图案吗？', '可以。请发送 AI、PDF、PNG 或 JPG 格式的图稿。我们会制作刺绣版，并在打样前和您确认效果。' )
					. bufan_block_details( '支持哪些付款方式？', '付款方式会在报价中确认，常用的有银行电汇（T/T）和 PayPal。' )
					. bufan_block_details( '怎么发货？', '根据您的时间和预算，可以走国际快递、空运或海运，也可以送到您在中国的货代仓库。' ),
			),
		),
		'contact'       => array(
			'template' => 'page-templates/contact.php',
			'en'       => array(
				'title'   => 'Contact',
				'slug'    => 'contact',
				'content' => bufan_block_p( 'Tell us about your project: the product, the quantity and any artwork you already have. We read every inquiry and reply by email.' ),
			),
			'zh'       => array(
				'title'   => '联系我们',
				'slug'    => 'lianxi',
				'content' => bufan_block_p( '告诉我们您的项目：想做什么产品、大概数量，以及已有的图案。我们会认真阅读每一条询盘，并通过邮件回复。' ),
			),
		),
		'privacy'       => array(
			'en' => array(
				'title'   => 'Privacy Policy',
				'slug'    => 'privacy-policy',
				'content' => bufan_block_p( 'This policy explains how ' . esc_html( $site ) . ' collects and uses personal data when you use this website.' )
					. bufan_block_h( 'What we collect' )
					. bufan_block_p( 'When you send an inquiry, we collect the details you enter in the form: your name and email address, and optionally your company, country, phone number, quantity and message.' )
					. bufan_block_h( 'How we use it' )
					. bufan_block_p( 'We use this information only to answer your inquiry and to prepare quotations and orders. We do not sell your data or share it with third parties for marketing.' )
					. bufan_block_h( 'How long we keep it' )
					. bufan_block_p( 'We keep inquiries for as long as needed to handle your request and any resulting order, and for any period required by law.' )
					. bufan_block_h( 'Cookies' )
					. bufan_block_p( 'This website only uses cookies that it needs to work, such as remembering your language. If we add analytics or advertising tools, we will update this policy.' )
					. bufan_block_h( 'Your rights' )
					. bufan_block_p( 'You can ask us at any time to see, correct or delete the personal data we hold about you. Contact us at the email address on our Contact page.' ),
			),
			'zh' => array(
				'title'   => '隐私政策',
				'slug'    => 'yinsi',
				'content' => bufan_block_p( '本政策说明 ' . esc_html( $site ) . ' 在您使用本网站时如何收集和使用个人信息。' )
					. bufan_block_h( '我们收集哪些信息' )
					. bufan_block_p( '当您发送询盘时，我们会收集您在表单中填写的信息：姓名和邮箱，以及您选填的公司、国家、电话、数量和留言。' )
					. bufan_block_h( '我们如何使用' )
					. bufan_block_p( '这些信息只用于回复您的询盘、准备报价和处理订单。我们不会出售您的信息，也不会为营销目的提供给第三方。' )
					. bufan_block_h( '保存多久' )
					. bufan_block_p( '询盘信息会保存到处理完您的需求及相关订单为止，以及法律要求的保存期限。' )
					. bufan_block_h( 'Cookie' )
					. bufan_block_p( '本网站只使用正常运行所必需的 Cookie，例如记住您选择的语言。如果以后加入统计或广告工具，我们会更新本政策。' )
					. bufan_block_h( '您的权利' )
					. bufan_block_p( '您可以随时要求查看、更正或删除我们保存的您的个人信息。请通过“联系我们”页面上的邮箱联系我们。' ),
			),
		),
		'blog'          => array(
			'en' => array(
				'title'   => 'Blog',
				'slug'    => 'blog',
				'content' => '',
			),
			'zh' => array(
				'title'   => '博客',
				'slug'    => 'boke',
				'content' => '',
			),
		),
	);
}

/**
 * Product categories: key => array( 'en' => [name, slug], 'zh' => [name, slug] ).
 *
 * @return array
 */
function bufan_starter_categories() {
	return array(
		'cosmetic' => array(
			'en' => array( 'Cosmetic Bags', 'cosmetic-bags' ),
			'zh' => array( '化妆包', 'huazhuangbao' ),
		),
		'coin'     => array(
			'en' => array( 'Coin Purses', 'coin-purses' ),
			'zh' => array( '零钱包', 'lingqianbao' ),
		),
		'pencil'   => array(
			'en' => array( 'Pencil Cases', 'pencil-cases' ),
			'zh' => array( '笔袋', 'bidai' ),
		),
		'tote'     => array(
			'en' => array( 'Tote Bags', 'tote-bags' ),
			'zh' => array( '托特包', 'tuotebao' ),
		),
	);
}

/**
 * Example products (with illustration images shipped in assets/img/samples).
 *
 * @return array
 */
function bufan_starter_products() {
	return array(
		array(
			'category' => 'cosmetic',
			'image'    => 'pouch.jpg',
			'meta'     => array(
				'model'    => 'BF-C101',
				'featured' => '1',
			),
			'en'       => array(
				'title'   => 'Embroidered Canvas Cosmetic Pouch',
				'slug'    => 'embroidered-canvas-cosmetic-pouch',
				'excerpt' => 'A roomy zip pouch in sturdy cotton canvas, embroidered with your logo or artwork. Ideal for makeup, toiletries and gift sets.',
				'content' => bufan_block_p( 'Our most popular shape: a flat-bottom pouch that stands on its own, with a smooth metal zipper and an easy-clean lining. The front panel is embroidered with your design.' )
					. bufan_block_h( 'Ideal for' )
					. bufan_block_list( array( 'Beauty and skincare brands', 'Gift sets and subscription boxes', 'Bridesmaid and wedding gifts' ) ),
				'meta'    => array(
					'size'           => '22 × 14 × 7 cm',
					'material'       => '12 oz cotton canvas, polyester lining',
					'colors'         => 'Natural, black, navy, or custom dyed',
					'embroidery'     => 'Flat or 3D puff',
					'moq'            => '100 pcs per design',
					'sample_time'    => '5–7 days',
					'lead_time'      => '15–25 days after sample approval',
					'packaging'      => '1 pc per OPP bag',
					'custom_options' => "Your logo or artwork embroidered on the front\nCustom canvas color\nPrinted or contrast lining\nWoven label and hang tag",
				),
			),
			'zh'       => array(
				'title'   => '刺绣帆布化妆包',
				'slug'    => 'cixiu-fanbu-huazhuangbao',
				'excerpt' => '厚实棉帆布制作的大容量拉链化妆包，可绣上您的 logo 或图案，适合装化妆品、洗漱用品和礼盒套装。',
				'content' => bufan_block_p( '我们最受欢迎的款式：平底设计可以自己立住，配顺滑的金属拉链和易清洁内衬，正面绣上您的图案。' )
					. bufan_block_h( '适用场景' )
					. bufan_block_list( array( '美妆护肤品牌', '礼盒和订阅盒', '伴娘礼物和婚礼回礼' ) ),
				'meta'    => array(
					'size'           => '22 × 14 × 7 厘米',
					'material'       => '12 安士棉帆布，涤纶内衬',
					'colors'         => '本色、黑色、藏青，或按需染色',
					'embroidery'     => '平绣或立体绣',
					'moq'            => '每款 100 个',
					'sample_time'    => '5–7 天',
					'lead_time'      => '确认样品后 15–25 天',
					'packaging'      => '每个独立 OPP 袋',
					'custom_options' => "正面刺绣您的 logo 或图案\n定制帆布颜色\n印花或撞色内衬\n织唛和吊牌",
				),
			),
		),
		array(
			'category' => 'coin',
			'image'    => 'coin-purse.jpg',
			'meta'     => array(
				'model'    => 'BF-P201',
				'featured' => '1',
			),
			'en'       => array(
				'title'   => 'Floral Embroidery Coin Purse',
				'slug'    => 'floral-embroidery-coin-purse',
				'excerpt' => 'A small canvas purse with detailed floral embroidery — perfect for gift shops, museum stores and souvenir shops.',
				'content' => bufan_block_p( 'Small enough for a pocket, big enough for coins, cards and earbuds. Fine flat embroidery brings illustrations and flowers to life on natural canvas.' ),
				'meta'    => array(
					'size'           => '12 × 9 cm',
					'material'       => '10 oz cotton canvas, cotton lining',
					'colors'         => 'Natural, oatmeal, sage, or custom dyed',
					'embroidery'     => 'Flat embroidery',
					'moq'            => '200 pcs per design',
					'sample_time'    => '5–7 days',
					'lead_time'      => '15–20 days after sample approval',
					'packaging'      => '1 pc per OPP bag',
					'custom_options' => "Your illustration or pattern\nKey ring or wrist strap\nCustom zipper color\nPrinted gift card",
				),
			),
			'zh'       => array(
				'title'   => '花卉刺绣帆布零钱包',
				'slug'    => 'huahui-cixiu-lingqianbao',
				'excerpt' => '精致花卉刺绣的帆布小零钱包，很适合礼品店、博物馆商店和纪念品店。',
				'content' => bufan_block_p( '小到可以放进口袋，又能装下硬币、卡片和耳机。细腻的平绣让插画和花朵在本色帆布上栩栩如生。' ),
				'meta'    => array(
					'size'           => '12 × 9 厘米',
					'material'       => '10 安士棉帆布，全棉内衬',
					'colors'         => '本色、燕麦色、鼠尾草绿，或按需染色',
					'embroidery'     => '平绣',
					'moq'            => '每款 200 个',
					'sample_time'    => '5–7 天',
					'lead_time'      => '确认样品后 15–20 天',
					'packaging'      => '每个独立 OPP 袋',
					'custom_options' => "您的插画或图案\n钥匙圈或手腕带\n定制拉链颜色\n印刷礼品卡",
				),
			),
		),
		array(
			'category' => 'pencil',
			'image'    => 'pencil-case.jpg',
			'meta'     => array(
				'model'    => 'BF-S301',
				'featured' => '1',
			),
			'en'       => array(
				'title'   => 'Custom Logo Canvas Pencil Case',
				'slug'    => 'custom-logo-canvas-pencil-case',
				'excerpt' => 'A slim canvas pencil case with your embroidered logo — great for schools, events, conferences and brand merchandise.',
				'content' => bufan_block_p( 'A clean, practical shape that fits pens, pencils and small tools. Your logo is embroidered on the front for a premium look that lasts.' ),
				'meta'    => array(
					'size'           => '21 × 8 × 5 cm',
					'material'       => '12 oz cotton canvas, polyester lining',
					'colors'         => 'Natural, black, navy, red',
					'embroidery'     => 'Flat embroidery',
					'moq'            => '200 pcs per design',
					'sample_time'    => '5–7 days',
					'lead_time'      => '15–20 days after sample approval',
					'packaging'      => '1 pc per OPP bag',
					'custom_options' => "Embroidered logo or name\nCustom canvas and zipper color\nLeather or woven zipper puller\nWoven label",
				),
			),
			'zh'       => array(
				'title'   => '定制 Logo 刺绣帆布笔袋',
				'slug'    => 'dingzhi-logo-fanbu-bidai',
				'excerpt' => '绣上您 logo 的简约帆布笔袋，适合学校、活动、会议和品牌周边。',
				'content' => bufan_block_p( '简洁实用的款式，可放笔和小工具。正面刺绣 logo，质感好又耐用。' ),
				'meta'    => array(
					'size'           => '21 × 8 × 5 厘米',
					'material'       => '12 安士棉帆布，涤纶内衬',
					'colors'         => '本色、黑色、藏青、红色',
					'embroidery'     => '平绣',
					'moq'            => '每款 200 个',
					'sample_time'    => '5–7 天',
					'lead_time'      => '确认样品后 15–20 天',
					'packaging'      => '每个独立 OPP 袋',
					'custom_options' => "刺绣 logo 或名字\n定制帆布和拉链颜色\n皮质或织带拉头\n织唛",
				),
			),
		),
		array(
			'category' => 'tote',
			'image'    => 'tote.jpg',
			'meta'     => array(
				'model'    => 'BF-T401',
				'featured' => '1',
			),
			'en'       => array(
				'title'   => 'Embroidered Canvas Tote Bag',
				'slug'    => 'embroidered-canvas-tote-bag',
				'excerpt' => 'A heavy canvas tote with a large embroidered design — a durable, reusable bag your customers will carry every day.',
				'content' => bufan_block_p( 'Heavy-duty canvas, reinforced handles and a large embroidery area for your artwork. Add an inner pocket or a zipper top to make it your own.' ),
				'meta'    => array(
					'size'           => '38 × 42 cm, handles 60 cm',
					'material'       => '16 oz cotton canvas',
					'colors'         => 'Natural, black, or custom dyed',
					'embroidery'     => 'Flat, appliqué or chenille',
					'moq'            => '100 pcs per design',
					'sample_time'    => '7 days',
					'lead_time'      => '20–25 days after sample approval',
					'packaging'      => '1 pc per OPP bag',
					'custom_options' => "Large embroidered design\nInner pocket or zipper top\nCustom handle length\nWoven label",
				),
			),
			'zh'       => array(
				'title'   => '刺绣帆布托特包',
				'slug'    => 'cixiu-fanbu-tuotebao',
				'excerpt' => '厚帆布托特包，大面积刺绣图案，耐用环保，顾客每天都愿意背。',
				'content' => bufan_block_p( '加厚帆布、加固提手，大面积刺绣区域展示您的图案。可加内袋或拉链口。' ),
				'meta'    => array(
					'size'           => '38 × 42 厘米，提手 60 厘米',
					'material'       => '16 安士棉帆布',
					'colors'         => '本色、黑色，或按需染色',
					'embroidery'     => '平绣、贴布绣或毛巾绣',
					'moq'            => '每款 100 个',
					'sample_time'    => '7 天',
					'lead_time'      => '确认样品后 20–25 天',
					'packaging'      => '每个独立 OPP 袋',
					'custom_options' => "大面积刺绣图案\n内袋或拉链口\n定制提手长度\n织唛",
				),
			),
		),
	);
}

/**
 * Default footer introduction text.
 *
 * @return array{en: string, zh: string}
 */
function bufan_starter_tagline() {
	return array(
		'en' => 'Family-run factory making custom embroidered canvas bags for brands and retailers in Europe and North America.',
		'zh' => '家族经营的帆布刺绣包工厂，为欧美品牌和零售商提供定制生产。',
	);
}
