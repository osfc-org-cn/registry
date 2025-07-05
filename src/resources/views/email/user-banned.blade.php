<center>
    <table cellpadding="0" cellspacing="0" border="0" width="100%" style="max-width: 600px; margin: 0 auto; font-family: 'Arial', sans-serif; color: #111827; background-color: #ffffff; border-radius: 8px; box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1); border: 1px solid #e5e7eb;">
        <tr>
            <td style="padding: 30px 20px; text-align: center; background: linear-gradient(135deg, #dc2626, #f97316); border-radius: 8px 8px 0 0;">
                <h1 style="color: #ffffff; font-size: 24px; margin: 0; font-weight: 600;">Account Status Notice</h1>
            </td>
        </tr>
        <tr>
            <td style="padding: 30px 20px; text-align: left;">
                <p style="font-size: 16px; line-height: 1.6; color: #4b5563; margin-bottom: 20px;">Hello {{ $username }},</p>
                
                <p style="font-size: 16px; line-height: 1.6; color: #4b5563; margin-bottom: 20px;">We regret to inform you that your account on {{ $webName }} has been suspended.</p>
                
                <div style="background-color: #f9fafb; border-left: 4px solid #dc2626; padding: 15px; margin-bottom: 20px; border-radius: 0 4px 4px 0;">
                    <p style="font-size: 16px; line-height: 1.6; color: #4b5563; margin: 0; display: flex; align-items: flex-start;">
                        <span style="margin-right: 10px; color: #dc2626;">&#9888;</span>
                        <span>Your account has been suspended and you will not be able to access your account features until this suspension is lifted.</span>
                    </p>
                </div>
                
                <p style="font-size: 16px; line-height: 1.6; color: #4b5563; margin-bottom: 20px;">If you believe this action was taken in error or have any questions, please contact our support team for assistance.</p>
                
                <p style="font-size: 16px; line-height: 1.6; color: #4b5563; margin-bottom: 20px;">Thank you for your understanding.</p>
            </td>
        </tr>
        <tr>
            <td style="padding: 20px; text-align: center; background-color: #f9fafb; border-top: 1px solid #e5e7eb; border-radius: 0 0 8px 8px;">
                <p style="font-size: 14px; color: #6b7280; margin: 0;">&copy; {{ date('Y') }} {{ $webName }}. All rights reserved.</p>
            </td>
        </tr>
    </table>
</center> 