<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
	<xsl:output method="html" encoding="UTF-8" indent="no" />
	<xsl:strip-space elements="*" />
	<xsl:template match="section">
	<html>
		<body>
			<xsl:apply-templates />
		</body>
	</html></xsl:template>
	<xsl:template match="paragraph">
		<p><xsl:apply-templates /></p>
	</xsl:template>
	<xsl:template match="emphasize">
		<em><xsl:apply-templates /></em>
	</xsl:template>
</xsl:stylesheet>